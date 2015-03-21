#!/usr/bin/env python
import re
'''
index:
0 import
1 datetime
2 file requested
3 referrer
4 User-Agent
'''

def process_log(log):
    requests = get_requests(log) #all lines of access log, to get first line: print requests[0]
    entry = get_entry(requests, 0) #feature selected
    totals = file_occur(entry)
    return totals

def get_requests(f):
    log_line = f.read()
    pat = (r''
           '(\d+.\d+.\d+.\d+)\s-\s-\s' #IP address
           '\[(.+)\]\s' #datetime
           '"GET\s(.+)\s\w+/.+"\s\d+\s' #requested file
           '\d+\s"(.+)"\s' #referrer
           '"(.+)"' #user agent
        )
    requests = find(pat, log_line, None)
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
    log_file = open('/var/log/nginx/access.log', 'r')

    #return dict of entry and total requests
    print(process_log(log_file))