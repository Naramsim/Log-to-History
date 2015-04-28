<link href="css/nfl.css" rel="stylesheet" >
<link href="css/chosen.min.css" rel="stylesheet" >
<script src="https://code.jquery.com/jquery-1.11.2.min.js" type="text/javascript"></script>
<script src="https://mtgfiddle.me/tirocinio/pezze/chosen.jquery.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js" type="text/javascript"></script>

<?php 
  ob_start();
  system("./main.py", $status);
  $output1 = json_decode( ob_get_clean() , true);
  $json_string = json_encode($output1, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
<body>
	<div id="summary">blablabla</div>
    <div id="graphic-and-annotations">
        <div id="search"></div>
        <div id="graphic-title-and-subtitle">
            <div id="graphic-title">User History since one hour ago</div>
            <div id="graphic-subtitle">IP switching pages are highlighted as:</div>
        </div>
        <!-- <div id="annotations"></div> -->
        <div id="graphic"></div>
        <div id="overlay"></div>
  	</div>
</body>

<script>
!function() {

    function e(e) {
        return function(t) {
            for (var a, n, r, o = t[0][0], i = t[0][1], s = [o, ",", i + e, "v", -2 * e], l = 0, c = t.length; ++l < c; )
                a = t[l][0], n = t[l][1], r = (i + n) / 2, s.push("C", o, ",", r, " ", a, ",", r, " ", a, ",", n + e, "v", -2 * e), o = a, i = n;
            return s.join("")
        }
    }

    var regInteger = /^\d+$/, t = [], a = [], r = 3500, o = 6000, n = d3.map();

    function isInteger( str ) {    
        return regInteger.test( str );
    }

    function findClosest(arr, id, increasing) {
        var step = increasing ? 1 : -1;
        var i=+id+step;
        if( arr[id]!="" && arr[id]!==undefined ){
            for(; i>=0 && i<=o; i+=step){
                if( arr[i] && arr[i]!=""){
                    return i;
                }
            }
        }
        return false; 
    }

    d3.tsv("story.tsv", function(e, data) {
        data.forEach(function(entry) {
            for (var key in entry) {
                if (entry[key]!="" ||entry[key]!==undefined){
                    previous_item = findClosest(entry,key,false);
                    next_item = findClosest(entry,key,true);
                    if (previous_item && next_item){
                        previous_item = previous_item + 1;
                        next_item = next_item - 1; //TODO:check if previous or next is the same page
                        if ( !(previous_item in entry) || entry[previous_item]=="" || entry[previous_item]===undefined)
                            entry[previous_item] = entry[key];

                        if ( !(next_item in entry) || entry[next_item]=="" || entry[next_item]===undefined)
                            entry[next_item] = entry[key];
                    }
                }
            }
        });
        var pages = new Set(); //store all folder requested
        var last_req = new Array(); //store all IP, and their last folder request
        data.forEach(function(entry) {            
            for (var key in entry) {
                if (entry.hasOwnProperty(key)) {
                    if( isInteger(key) ){
                        if(typeof entry[key] !== "undefined"){
                            if(entry[key] != ""){
                                pages.add(entry[key])
                                if( !(entry[key] in last_req) ){
                                    last_req[ entry[key] ] = key;
                                }else{
                                    if (last_req[ entry[key] ] < key)  last_req[ entry[key] ] = key;
                                }
                            }
                        }
                    }
                }
            }
        });
        pages.delete("");
        index = -1; //sets does not have indexes
        pages.forEach(function(value) {
            var t_element = new Object();
            t_element.id = value;
            t_element.adjust = -5;
            t_element.name = value;
            t_element.index = ++index;
            t.push(t_element);

            var a_element = new Object();
            a_element.id = value;
            a_element.orient = "top";
            a_element.label = value;
            a_element.year = last_req[value];
            a_element.index = index;
            a.push(a_element);
        });
        n = d3.map();
        t.forEach(function(e, t) {
            e.index = t, n.set(e.id, e)
        });
        draw(data);
    });
    
    function draw(t) {
        var data = t
        var margins = {top: 40.5,right: 35.5,bottom: 40.5,left: 65.5}, 
            i = 4230 - margins.left - margins.right,
            s = 1096 - margins.top - margins.bottom, 
            l = d3.time.scale().domain([new Date(r, 0, 1), new Date(o, 0, 1)]).range([i, 0]), 
            c = d3.scale.linear().rangeRound([20, s]), 
            d = d3.svg.line().interpolate(e(4.5)).defined(function(e) {
            return e.conference
        }).y(function(e) {
            return l(e.date)
        }).x(function(e) {
            return c(e.y)
        }), p = d3.select("#graphic").append("svg").attr("height", i + margins.left + margins.right).attr("width", s + margins.top + margins.bottom).append("g").attr("transform", "translate(" + margins.top + "," + margins.left + ")");
        /* draw horizontal lines */p.append("defs").append("marker").attr("id", "arrowhead").attr("viewBox", "-.1 -5 10 10").attr("orient", "auto").attr("markerWidth", 3).attr("markerHeight", 3).append("path").attr("d", "M-.1,-4L3.9,0L-.1,4"), p.append("g").attr("class", "axis axis--minor").attr("transform", "translate(" + s + ",0)").call(d3.svg.axis().scale(l).orient("right").tickSize(-s).ticks(d3.time.year)).selectAll(".tick").attr("class", function(e) {
            return "tick tick--" + (1984 === e.getFullYear() ? 1984 : e.getFullYear() % 10 ? "minor" : "major") //in 1984 draws a marker
        }), p.append("g").attr("class", "axis axis--major").attr("transform", "translate(" + s + ",0)").call(d3.svg.axis().scale(l).orient("right").tickValues(l.ticks(d3.time.year, 100).concat(l.domain()))), p.append("g").attr("class", "axis axis--major").call(d3.svg.axis().scale(l).orient("left").tickValues(l.ticks(d3.time.year, 50).concat(l.domain())));
        var u = d3.select("#overlay").append("svg").attr("height", i + margins.left + margins.right).attr("width", s + margins.top + margins.bottom).append("g").attr("transform", "translate(" + margins.top + "," + margins.left + ")"), h = d3.select("#graphic-subtitle").append("svg").style("position", "absolute").style("margin-top", "-5px").attr("height", 30).attr("width", 30), f = h.append("g").attr("class", "school school--switch"), y = f.append("linearGradient").attr("id", "school-switch-gradient-key").attr("y1", "100%").attr("y2", "0%").attr("x1", 0).attr("x2", 0);
        y.append("stop").attr("offset", "0%").attr("stop-color", "#d7d7d7"), y.append("stop").attr("offset", "100%").attr("stop-color", "purple"), f.append("path").attr("d", "M" + e(1)([[10, 22], [20, 8]])).style("stroke", "url(#school-switch-gradient-key)"), 
        !function(t) {
            //console.log(t)
            function h(e) { //hover
                if (e) {
                    f(e.school.name);
                    var t = c(e.y), a = t > 860;
                    B.style("display", null).attr("transform", "translate(" + (a ? t - 2 : t + 2) + "," + Math.round(l(e.date)) + ")rotate(0)").interrupt().transition().ease("elastic").attr("transform", "translate(" + (a ? t - 2 : t + 2) + "," + Math.round(l(e.date)) + ")rotate(" + (a ? 15 : -15) + ")"), M.style("text-anchor", a ? "end" : "start").attr("x", a ? -10 : 10).text(e.school.name).style("font-weight", "700").append("tspan").style("font-weight", "500").text(" " + e.school.team);
                    var n = M.node().getComputedTextLength() + 5;
                    j.attr("d", a ? "M0,0l-10,-10h" + -n + "v20h" + n + "z" : "M0,0l10,-10h" + n + "v20h-" + n + "z")
                } else
                    f(null), B.style("display", "none")
            }
            function f(e) {
                E.classed("school--hover-hover", function(t) {
                    return t.name === e
                })
            }
            function y(e) {
                d3.selectAll(".school--switch-selected").classed("school--switch-selected", !1).select("stop:first-child").attr("stop-color", "#d7d7d7"), C.classed("school--session-selected", function(t) {
                    return t[0].school.name === e
                }), A.filter(function(t) {
                    return t[0].school.name === e
                }).each(function() {
                    this.parentNode.appendChild(this)
                }).classed("school--switch-selected", !0).select("stop:first-child").attr("stop-color", "orange"), $(".search-select").val(e).trigger("chosen:updated")
            }
            var g = [], m = [], b = [];
            t = t.filter(function(e) {
                for (var t = r; o >= t; ++t)
                    if (n.get(e[t]))
                        return !0
            }), t.forEach(function(e) {
                for (var t, a = e.years = [], i = [], s = r; o >= s; ++s) {
                    var l = n.get(e[s]), c = {school: e,conference: l,year: s,date: new Date(s, 0, 1)};
                    l ? (t === l && i.forEach(function(e) {
                        e.conference = l
                    }), i = [], t = l) : e[s] || i.push(c), a.push(c), g.push(c)
                }
            }), t.forEach(function(e) {
                var t, a = 0, n = e.years.length, r = e.years[0], o = [r];
                for (r.conference && m.push(o); ++a < n; )
                    if (t = r, r = e.years[a], t.conference === r.conference)
                        o.push(r);
                    else if (o = [r], r.conference) {
                        if (t.conference)
                            b.push([t, r]);
                        else {
                            var i = [r];
                            i.enter = !0, b.push(i)
                        }
                        m.push(o)
                    } else {
                        var i = [t];
                        i.exit = !0, b.push(i)
                    }
            }), m.forEach(function(e) {
                e.forEach(function(t) {
                    t.session = e
                });
                var t = e.startYear = e[0].year, a = e.endYear = e[e.length - 1].year, r = e[0].school, o = r[t - 1], i = r[a + 1];
                e.previousIndex = n.has(o) ? n.get(o).index : -1, e.nextIndex = n.has(i) ? n.get(i).index : -1
            });
            var v = 6, x = d3.nest().key(function(e) {
                return e.conference.id
            }).sortKeys(function(e, t) {
                return n.get(e).index - n.get(t).index
            }).key(function(e) {
                return e.year
            }).sortKeys(function(e, t) {
                return e - t
            }).entries(g.filter(function(e) {
                return e.conference
            }));
            x.forEach(function(e) {
                e.maxSize = Math.max(8, d3.max(e.values, function(e) {
                    return e.values.length
                })) + e.values[0].values[0].conference.adjust
            }), c.domain([0, (x.length - 1) * v + d3.sum(x, function(e) {
                    return e.maxSize
                })]);
            var w = 0;
            x.forEach(function(e) {
                e.basePosition = w, e.values.forEach(function(e) {
                    e.values.sort(function(e, t) {
                        return d3.ascending(e.session.startYear, t.session.startYear) || d3.ascending(t.session.endYear, e.session.endYear) || d3.ascending(e.session.previousIndex, t.session.previousIndex) || d3.ascending(e.session.nextIndex, t.session.nextIndex) || d3.ascending(e.school.name, t.school.name)
                    }).forEach(function(e, t) {
                        e.y = w + t
                    })
                }), w += e.maxSize + v
            }), d3.select("#search").on("click", function() {
                d3.event.stopPropagation()
            }).append("select").attr("class", "search-select").attr("data-placeholder", "Select a IP to highlight.").attr("tabindex", 2).selectAll("option").data([{name: "",team: ""}].concat(t.sort(function(e, t) {
                return d3.ascending(e.name, t.name)
            }))).enter().append("option").attr("value", function(e) {
                return e.name
            }).text(function(e) {
                return e.name// + " " + e.team
            }), $(".search-select").chosen({width: "100%",allow_single_deselect: !0}).change(
            function() {
                y(this.value)
            }), p.append("g").attr("class", "school school--session-halo").selectAll("path").data(m).enter().append("path").attr("d", d);
            var C = p.append("g").attr("class", "school school--session").selectAll("path").data(m).enter().append("path").attr("d", d).classed("school--session-partial", function(e) {
                return "Notre Dame" === e[0].school.name
            });
            p.append("g").attr("class", "school school--switch-halo").selectAll("path").data(b.filter(function(e) {
                return 2 === e.length
            })).enter().append("path").attr("d", d);
            var A = p.append("g").attr("class", "school school--switch").selectAll("g").data(b.sort(function(e, t) {
                return t[t.length - 1].year - e[e.length - 1].year
            })).enter().append("g").attr("class", function(e) {
                return e.enter ? "school--switch--enter" : e.exit ? "school--switch--exit" : null
            }), S = A.append("linearGradient").attr("gradientUnits", "userSpaceOnUse").attr("id", function(e, t) {
                return "school-switch-gradient-" + t
            }).attr("y1", function(e) {
                return l(e[0].date) + (e[1] ? 0 : e.enter ? -5 : 5)
            }).attr("y2", function(e) {
                return e[1] ? l(e[1].date) : l(e[0].date) + (e.enter ? 5 : -5)
            }).attr("x1", 0).attr("x2", 0);
            S.append("stop").attr("offset", "0%").attr("stop-color", function(e) {
                return e.enter || e.exit ? "purple" : "#d7d7d7"
            }).attr("stop-opacity", function(e) {
                return e.enter || e.exit ? 0 : 1
            }), S.append("stop").attr("offset", "100%").attr("stop-color", "purple"), A.append("path").attr("d", d).style("stroke", function(e, t) {
                return "url(#school-switch-gradient-" + t + ")"
            });
            var E = p.append("g").attr("class", "school school--hover").selectAll("path").data(t).enter().append("path").attr("d", function(e) {
                return d(e.years)
            });
            p.selectAll(".conference-label").data(a).enter().append("text").attr("class", function(e) {
                return "conference-label conference-label--" + e.year
            }).each(function(e) {
                var t = e.label.split(" ");
                e.labelWords = t.map(function(a, n) {
                    return {word: a,offset: "top" === e.orient ? n - t.length : n + 1.71}
                })
            }).attr("transform", function(e) {
                
                var t = Math.max(0, Math.min(i, l(new Date(e.year,0,1)))), a = x.filter(function(t) {
                    return t.key === e.id
                })[0].values.filter(function(t) {
                    return t.key == e.year
                })[0].values;
                return a = (a[0].y + a[a.length - 1].y) / 2, "translate(" + c(a) + "," + t + ")"
            }).selectAll("tspan").data(function(e) {

                return e.labelWords
            }).enter().append("tspan").attr("x", 0).attr("y", function(e) {
                return 1.1 * e.offset + "em"
            }).text(function(e) {
                return e.word
            }), p.append("line").attr("class", "annotation-line").attr("x1", 865).attr("y1", 25).attr("x2", 890).attr("y2", 25), p.append("line").attr("class", "annotation-line").attr("x1", 855).attr("y1", 428).attr("x2", 975).attr("y2", 428), p.append("line").attr("class", "annotation-line").attr("x1", 185).attr("y1", 1320).attr("x2", 185).attr("y2", 1370);
            
            var B = u.append("g").attr("class", "tooltip").style("display", "none"), j = B.append("path"), M = B.append("text").attr("dy", ".35em").attr("x", 10);
            u.append("g").attr("class", "voronoi").selectAll("path").data(d3.geom.voronoi().y(function(e) {
                return l(e.date)
            }).x(function(e) {
                return c(e.y)
            }).clipExtent([[-40, -40], [s + 40, i + 40]])(g.filter(function(e) {
                return !isNaN(e.y)
            }))).enter().append("path").attr("d", function(e) {
                return "M" + e.join("L") + "Z"
            }).on("mouseover", function(e) {
                h(e.point)
            }).on("mouseout", function() {
                h(null)
            }).on("click", function(e) {
                f(null), y(e.point.school.name), d3.event.stopPropagation()
            }), d3.select(window).on("click", function() {
                y(null)
            })
        }(data);
    }
}(window);
</script>