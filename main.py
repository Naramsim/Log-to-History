#!/usr/bin/env python
import re
from collections import defaultdict
import json
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

def process_log(log):
    requests = get_requests(log) #all lines of access log, to get first line: print requests[0]
    entry = get_entry(requests, 0) #feature selected
    totals = file_occur(entry)
    return totals

def get_user_story(log):
    requests = get_requests(log)
    story = defaultdict(list) #dict of array/list of dicts
    for req in requests:
        elements = {}
        elements["datetime"]=req[1]
        elements["file_requested"]=req[2]
        elements["referrer"]=req[5]
        elements["size"]=req[4]
        elements["UA"]=req[6] # we can put User-Agent for checking if it is the same person to use this IP (maybe there could be a NAT)
        story[req[0]].append(elements)
    return json.dumps( story, sort_keys=False)

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
    log_file = open('access.log', 'r')

    #return dict of entry and total requests
    print get_user_story(log_file)