/*Data sample:
{ 
      "key" : "/atleta" , 
      "values" : [ [ 1025409600000 , 23.041422681023] , [ 1028088000000 , 19.854291255832],
       [ 1030766400000 , 21.02286281168], 
       [ 1033358400000 , 22.093608385173],
       [ 1036040400000 , 25.108079299458],
       [ 1038632400000 , 26.982389242348]
       ...

*/

function prepare_stack(){
  $("#chart").empty();

  var colors = d3.scale.category20();
  var keyColor = function(d, i) {return colors(d.key)};
  var chart;
  var regInteger = /^\d+$/;
  var max=0, min=0;
  var time_interval = 30;//seconds
  var data_folders = []
  function isInteger( str ) {
    /*regex that checks if string can be considered as int interger*/
    return regInteger.test( str );
  }
  function findClosest(arr, id, increasing) {
      /*
      method that, in a dictionary(associative array), finds the element after the passed one
      */
      var step = increasing ? 1 : -1; //search next or previous
      var i=+id+step;
      if( arr[id]!="" && arr[id]!==undefined ){
          //console.log(o)
          for(; i>=0 && i<=max; i+=step){
              if( arr[i] && arr[i]!=""){
                  return i;
              }
          }
      }
      return false;
  }
  function createIntervalTable() {
      times_array = []; //array with all intervals
      for (var i=min; i<max; i+=time_interval){ //building this array
          times_array.push([ i, 0 ]);
      }
      return times_array;
  }

  d3.json("data/stack.json" , function(data) {
  	//console.log(data)
    var pages = new Set(); //store all folder requested by all visitors 
    data["data"].forEach(function(entry) {            
        for (var key in entry) {
  			if (entry.hasOwnProperty(key)) {
  				if ( isInteger(key) ){
  					if(typeof entry[key] !== "undefined"){ //TODO: collapse ifs
  					  if(entry[key] != ""){
  					      pages.add(entry[key]);
  					      if(+key > max) max = key; //finds maximum time-value
  					  }
  					}
  				}
  			}
        }
    });
    pages.delete("");
    
    pages.forEach(function(entry){
      folder_object = new Object();
      folder_object["key"] = entry;
      folder_object["values"] = createIntervalTable();
      data_folders.push(folder_object);
    });
    
    folder_index = new Object();//index of folders in data_folders
    i=0;
    data_folders.forEach(function(entry){
      folder_index[entry["key"]] = i++;
    });

    //filling each time interval with the proper value
    data["data"].forEach(function(entry){
      for (var key in entry) {
        if (entry.hasOwnProperty(key) && isInteger(key) && typeof entry[key] !== "undefined" && entry[key] != ""){
          next_item = findClosest(entry, key, true)
          folder = entry[key]
          //console.log(data_folders[folder_index[folder]]["values"])
          has_started = false;
          //console.log("starting with key="+key)
          data_folders[folder_index[folder]]["values"].every(function(interval){
              if(+key >= +interval[0] && +key <= +interval[0]+time_interval){
                  interval[1]++;
                  //console.log("plus 1 for "+folder+" starting in "+ interval[0]+", ending in "+ next_item)
                  has_started = true;
                  return true; 
              }
              else if(has_started && +next_item > +interval[0]+time_interval){
                  interval[1]++;
                  //console.log("plus 1 for "+folder+" continuing to"+ interval[0]);
                  return true;
              }
              else if(has_started && +next_item >= +interval[0] && +next_item <= +interval[0]+time_interval){
                  //console.log(folder+" ending to"+ interval[0]);
                  return false; //break
              }
              else{
                  return true;
              }
          })
          /*console.log(key)
          console.log(next_item)
          console.log("---")*/
        }
      }
    });
    //console.log(data_folders);
    datam = JSON.parse(JSON.stringify(data_folders))

    nv.addGraph(function() {
        chart = nv.models.stackedAreaChart()
            .useInteractiveGuideline(true)
            .x(function(d) {/*console.log(d[0]);*/ return new Date( (d[0]*1000) + data["start_time"] - 3600000) })//convert to local timestamp
            .y(function(d) { return d[1]; })
            .controlLabels({stacked: "Stacked"})
            .color(keyColor)
            .duration(300);
        //chart.xScale = d3.time.scale();
        chart.xAxis.tickFormat(function(d) {return d3.time.format('%H:%M:%S')(new Date(d)) });
        
        chart.yAxis.tickFormat(d3.format(',.2f'));
        d3.select('#chart')
            .datum(datam)
            .transition().duration(1000)
            .call(chart)
            .each('start', function() {
                setTimeout(function() {
                    d3.selectAll('#chart1 *').each(function() {
                        if(this.__transition__)
                            this.__transition__.duration = 1;
                    })
                }, 0)
            });
        nv.utils.windowResize(chart.update);
        end_spinner();
        return chart;
    });
  });
}

