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
  $("#chart").empty(); // clean the chart

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
    /*
    method that creates a list of discrete intervals, starting from 0 ending to last time
    */
      times_array = []; //array with all intervals
      console.log(max)
      for (var i=min; i<max; i+=time_interval){ //building this array
          times_array.push([ i, 0 ]);
      }
      return times_array;
  }

  d3.json("data/s"+string_name+".json" , function(data) {
  	//console.log(data)
    var pages = new Set(); //store all folder requested by all visitors 
    data["data"].forEach(function(entry) {            
        for (var key in entry) {
  			if (entry.hasOwnProperty(key)) {
  				if ( isInteger(key) ){
  					if(typeof entry[key] !== "undefined"){ //TODO: collapse ifs
  					  if(entry[key] != ""){
  					      pages.add(entry[key]);
  					      if(+key > max) max = +key; //finds maximum time-value
  					  }
  					}
  				}
  			}
        }
    });
    pages.delete("");

    // calculates best time_interval
    if (data["interval_processed"] > 86400){
      time_interval = +((data["interval_processed"]/180).toFixed(0))
    }else{
      time_interval = +((data["interval_processed"]/360).toFixed(0))
    }
    //console.log(time_interval)
    
    pages.forEach(function(entry){
      folder_object = new Object(); //dict for folder
      folder_object["key"] = entry; //entry is the name of the folder
      folder_object["values"] = createIntervalTable();
      data_folders.push(folder_object); // all dict of folders
    });
    
    folder_index = new Object();//index of folders in data_folders for fast access
    i=0;
    data_folders.forEach(function(entry){
      folder_index[entry["key"]] = i++;
    });

    //filling each time interval with the proper value
    data["data"].forEach(function(entry){ //entry is the history of a user
      var entry_sorted_keys = Object.keys(entry).sort( function(a,b) { //sorting object elements for fast access to the next element
          return +b - +a; //desc ordering
      });
      //console.log(entry_sorted_keys)
      for (var key in entry) { //for each user visits
        if (entry.hasOwnProperty(key) && isInteger(key) && typeof entry[key] !== "undefined" && entry[key] != ""){
          var current_key_index =  entry_sorted_keys.indexOf(key);
          next_item = current_key_index > 0 ?  entry_sorted_keys[current_key_index -1] : false //get next item
          //next_item_ = findClosest(entry, key, true)
          folder = entry[key]
          has_started = false;
          data_folders[folder_index[folder]]["values"].every(function(interval){ // adds to the list of intervals the correct page visited
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
        }
      }
    });

    datam = JSON.parse(JSON.stringify(data_folders)) //bug?

    nv.addGraph(function() {
        chart = nv.models.stackedAreaChart()
            .useInteractiveGuideline(true) //vertical line with current data
            .x(function(d) { return new Date( (d[0]*1000) + data["start_time"] - 3600000) })//convert to local timestamp
            .y(function(d) { return d[1]; })
            .controlLabels({stacked: "Stacked"}) //default option
            .color(keyColor) //gives the colors to the areas
            .duration(300); //animation
        //chart.xScale = d3.time.scale();
        chart.xAxis.axisLabel('Time').rotateLabels(25).tickFormat(function(d) {return d3.time.format('%e/%m %H:%M:%S')(new Date(d)) }); //set time format and rotates the labels
        
        chart.yAxis.axisLabel('Visitors');
        chart.yAxisTickFormat(d3.format(',.0d')); //set dot notation
        d3.select('#chart')
            .datum(datam)
            .transition().duration(1000)
            .call(chart) //creates the chart
            .each('start', function() { //after initial animation, now animation is resetted
                setTimeout(function() {
                    d3.selectAll('#chart1 *').each(function() {
                        if(this.__transition__)
                            this.__transition__.duration = 1;
                    })
                }, 0)
            });
        nv.utils.windowResize(chart.update); //resize the chart
        end_spinner(); 
        return chart;
    });
  });
}

