#!/usr/bin/env python
import re, sys, json, csv
from collections import defaultdict
from dateutil import parser
'''
index:
0 ip
1 datetime
2 file requested
3 status
4 size of response in bytes
5 referrer
6 User-Agent
'''

my_site = "://atletica.me"
protocol = "http"

def process_log(log):
    requests = get_requests(log) #all lines of access log, to get first line: print requests[0]
    entry = get_entry(requests, 0) #feature selected
    totals = file_occur(entry)
    return totals

def get_user_story(log):
    try:
        IPs = [] # list of processed IPs
        IP_index = 0 # unuseful
        my_site = "://atletica.me"
        protocol = "http"
        filters = [".php",".htlm",".htm"]
        requests = get_requests(log) #list with all lines of the access log

        story = {} #dict of array/list of dicts
        story["name"] = "root_log" # root of the three
        story["children"] = [] # default_dict(list) is an alternative

        tsv_list = []
        tsv_list_keys = {}
        key_index = 0
        hours = []
        hour_changed = False

        json_metric = []

        for req in requests:           
            if ( any(x in req[2] for x in filters) or (req[2].endswith('/')) or (('.') not in req[2]) ): #if page requested is contained in filters or it is a folder
            	
                # preparing JSON tree
            	IP_index_list = [n for n,el in enumerate(story["children"]) if el["name"] == req[0]]  # IP_index_list is a one-value list, it contains the index of the IP that we are processing
                # IF IP ALREADY PROCESSED ONCE
                if req[0] in IPs:
                    is_ip_new = False
                    IP_index = IP_index_list[0]
                # IF IP IS NEW
                else:
                    is_ip_new = True
                    IPs.append(req[0]) #now it is no more a new IP
                    ip_dict = {}
                    ip_dict["name"] = req[0]
                    ip_dict["UA"] = req[6]
                    ip_dict["datetime"] = req[1]
                    ip_dict["children"] = []
                    ip_dict["count"] = 0
                    ip_dict["is_bot"] = check_bot(req)
                    story["children"].append(ip_dict)
                    IP_index = len(story["children"]) - 1

                    json_metric.append([])
                story["children"][IP_index]["count"] += 1
                if my_site not in req[5] :   # if referrer is not defined or come from another site, I create a new first-level node
                    story["children"][IP_index]["children"].append({"name":req[2], "ref":req[5], "children":[], "datetime":req[1]}) #, "children":[]
                else:	#if not, i try to chain it
                	attach_node( story["children"][IP_index]["children"], req )

                #preparing tsv chart 
                '''
                tsv_dict = {}
                if req[2] not in tsv_list_keys:
                    tsv_list_keys[req[2]] = key_index
                    key_index = key_index + 1
                full_data = parser.parse(req[1],fuzzy=True)
                tsv_dict["date"] = full_data.strftime('%Y/%m/%d %H:%M:%S')
                #tsv_dict["file_requested"] = req[2]
                #tsv_dict["referrer"] = req[5]
                #tsv_dict["point_height"] = tsv_list_keys[req[2]]
                tsv_dict[req[0].replace(".","-")] = tsv_list_keys[req[2]]
                tsv_list.append(tsv_dict)'''

                #preparing tsv flow-chart
                tsv_dict = {}
                full_data = parser.parse(req[1],fuzzy=True)
                hour = full_data.strftime('%M%S')
                folder_requested = "/" + req[2].split("/")[1]

                if len(hours)<2: #for the first two elements, add them anyway
                    hours.append(hour)
                    if is_ip_new:
                        #print req[0]
                        tsv_dict["name"] = req[0]
                        tsv_dict["team"] = req[0]
                        tsv_dict[hour] = folder_requested
                        tsv_list.append(tsv_dict)
                    else:
                        current_dict = search_in_list(req[0],tsv_list)
                        current_dict[hour] = folder_requested
                else:
                    if (len(hours)>2 and hours[-1] > hour) and not hour_changed:
                        #print hours[-1]+" "+hour
                        hour_changed = True

                    if not hour_changed:
                        hours.append(hour)

                    if not hour_changed:
                        if not is_ip_new:
                            current_dict = search_in_list(req[0],tsv_list)
                            current_dict[hour] = folder_requested
                        else:
                            #print req[0]
                            #print "s"
                            tsv_dict["name"] = req[0]
                            tsv_dict["team"] = req[0]
                            tsv_dict[hour] = folder_requested
                            tsv_list.append(tsv_dict)
                
                '''
                #preparing JSON MetricGraphics chart
                metric_value = {}
                metric_value["date"] = tsv_dict["date"]
                metric_value["value"] = tsv_list_keys[req[2]]
                metric_value["file_requested"] = req[2]
                metric_value["ip"] = req[0]
                json_metric[IP_index].append(metric_value)'''


    except Exception as ex:
        print( "[" + str(format( sys.exc_info()[-1].tb_lineno )) + "]: " + str(ex) )    # error line and exception
        exit(1)
    #CREATE JSON for Tree
    ret = json.dumps( story, sort_keys=False)
    #print ret
    file_ = open('accesslog.json', 'w')
    file_.write(ret)
    file_.close()
    
    
    # CREATE TSV fr Chart
    if(hours[-2] > hours[-1]):
    	del hours[-1]
    keys = list(hours)
    '''for ip in IPs:
        keys.append( ip.replace(".","-") )'''
    keys.insert(0,"name")
    keys.insert(1,"team")
    #print tsv_list
    with open('story.tsv', 'wb') as output_file:
        dict_writer = csv.DictWriter(output_file, keys,extrasaction='ignore',delimiter="\t")
        dict_writer.writeheader()
        dict_writer.writerows(tsv_list)

    #CREATE JSON for MetricGraphics Chart
    #print json.dumps( json_metric, sort_keys=False)
    ret = json.dumps( json_metric, sort_keys=False) 
    #print ret
    file_ = open('simple.json', 'w')
    file_.write( ret )
    file_.close()


def attach_node(current_node, req):
    if not current_node:
        return False
    ref_index_list = [n for n,el in enumerate(current_node) if (protocol+my_site+el["name"]) == req[5]]    #the referrer contains the full URL
    if ref_index_list:
        ref_index = ref_index_list[-1]  #last element #TODO check between the two datetime
        #story["children"][IP_index]["children"][ref_index]["children"].append({"name":req[2], "ref":req[5], "datetime":req[1]})
        current_node[ref_index]["children"].append({"name":req[2], "ref":req[5], "datetime":req[1], "children":[]})
    elif not ref_index_list: #if we have not found the referrer maybe it is in a son
        for element in current_node:    #for every son, do:
            attach_node(element["children"] , req)


def check_bot(request):
	if("robot.txt" in request[0]):
		return True
	bots = ["bot","crawl","spider"]
	if any(bot in request[6] for bot in bots):
		return True
	return False

def search_in_list(name, _list):
    for p in _list:
        #print name + " "+ p['name']
        if p['name'] == name:
            return p


def get_requests(f):
    log_line = f.read()
    pat = (r''
            '(\d+.\d+.\d+.\d+)\s-\s-\s' #IP address
            '\[(.+)\]\s' #datetime
            '"GET\s(.+)\s\w+/.+"\s' #requested file
            '(\d+)\s' #status
            '(\d+)\s' #bandwidth
            '"(.+)"\s' #referrer
            '"(.+)"' #user agent
        )
    requests = find(pat, log_line, None)
    #print requests
    return requests #array of all access log lines

def find(pat, text, match_item):
    match = re.findall(pat, text)
    if match:
        return match
    else:
        return False

def get_entry(requests,index):
    #get requested entry with req
    requested_entries = []
    for req in requests:
        #req[2] for req file match, change to
        #data you want to count totals
        requested_entries.append(req[index])
    return requested_entries #select only one feature from all, index is the feature we want

def file_occur(entry):
    #number of occurrences over requested entry with related entry
    d = {}
    for file in entry:
        d[file] = d.get(file,0)+1
    return d

if __name__ == '__main__':

    #nginx access log, standard format
    log_file = open('atletica2.log', 'r')

    #return dict of entry and total requests
    ret = get_user_story(log_file)
    


'''
COMMENTS:
every line of log that have a referrer pointing at your site, but it has no father, is omitted
'''