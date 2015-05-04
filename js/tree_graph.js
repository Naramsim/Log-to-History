/*
prepare_graph() is the main function which build a tree by parsing a JSON, the last one has been prepared by the script "main.py"
this function code is mainly taken by http://bl.ocks.org/mbostock/4339083
We have only adapted the code to fit our data and added tooltips on node hover
*/
function prepare_graph(){
  var margin = {top: 20, right: 120, bottom: 20, left: 120},
      width = 2600 - margin.right - margin.left,
      height = 1000 - margin.top - margin.bottom;
      
  var i = 0,
      duration = 750,
      root;

  var tree = d3.layout.tree()
      .size([height, width]);

  var diagonal = d3.svg.diagonal()
      .projection(function(d) { return [d.y, d.x]; });

  var tip = d3.tip()
    .attr('class', 'd3-tip')
    .offset([-10, 0])
    .html(function(d) {
      if(d.UA)
        return " <span style='color:lightsteelblue'>" + d.UA +" "+ d.datetime + "</span>";
      return " <span style='color:lightsteelblue'>" + d.datetime + "</span>";
    })

  var svg = d3.select("body").append("svg")
      .attr("width", width + margin.right + margin.left)
      .attr("height", height + margin.top + margin.bottom)
    .append("g")
      .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

  svg.call(tip);

  d3.json("accesslog.json", function(error, flare) {
    root = flare;
    root.x0 = height / 2;
    root.y0 = 0;

    function collapse(d) {
      if (d.children) {
        d._children = d.children;
        d._children.forEach(collapse);
        d.children = null;
      }
    }

    root.children.forEach(collapse);
    update(root);
  });

  d3.select(self.frameElement).style("height", "800px");

  function update(source) {

    // Compute the new tree layout.
    var nodes = tree.nodes(root).reverse(),
        links = tree.links(nodes);

    // Normalize for fixed-depth.
    nodes.forEach(function(d) { d.y = d.depth * 180; });

    // Update the nodes…
    var node = svg.selectAll("g.node")
        .data(nodes, function(d) { return d.id || (d.id = ++i); });

    // Enter any new nodes at the parent's previous position.
    var nodeEnter = node.enter().append("g")
        .attr("class", "node")
        .attr("transform", function(d) { return "translate(" + source.y0 + "," + source.x0 + ")"; })
        .on("click", click);


    nodeEnter.append("circle")
        .attr("r", 1e-6)
        .style("fill", function(d) { 
        	return d._children ? "lightsteelblue" : "#fff"; 
        });

    nodeEnter.append("text")
        .attr("x", function(d) { return has_child(d) ? 10 : -10; })
        .attr("dy", ".35em")
        .attr("text-anchor", function(d) {
        	return has_child(d) ? "start" : "end";
        })
        .text(function(d) { return d.name; })
        .style("fill-opacity", 1e-6)
        .style("font-size", function(d) {
        	return (d.name.length > 20) ? "10px" : "14px";
        })
        .on('mouseover', tip.show)
        .on('mouseout', tip.hide);

    // Transition nodes to their new position.
    var nodeUpdate = node.transition()
        .duration(duration)
        .attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; });

    nodeUpdate.select("circle")
        .attr("r", 4.5)
        .style("fill", function(d) { 
        	if (d._children == null || d._children === undefined || d._children.length < 1)
				return "#fff";
			if (d.is_bot)
				return "#F9DC69";
			return "lightsteelblue";
			
        });

    nodeUpdate.select("text")
        .style("fill-opacity", 1);

    // Transition exiting nodes to the parent's new position.
    var nodeExit = node.exit().transition()
        .duration(duration)
        .attr("transform", function(d) { return "translate(" + source.y + "," + source.x + ")"; })
        .remove();

    nodeExit.select("circle")
        .attr("r", 1e-6);

    nodeExit.select("text")
        .style("fill-opacity", 1e-6);

    // Update the links…
    var link = svg.selectAll("path.link")
        .data(links, function(d) { return d.target.id; });

    // Enter any new links at the parent's previous position.
    link.enter().insert("path", "g")
        .attr("class", "link")
        .attr("d", function(d) {
          var o = {x: source.x0, y: source.y0};
          return diagonal({source: o, target: o});
        });

    // Transition links to their new position.
    link.transition()
        .duration(duration)
        .attr("d", diagonal);

    // Transition exiting nodes to the parent's new position.
    link.exit().transition()
        .duration(duration)
        .attr("d", function(d) {
          var o = {x: source.x, y: source.y};
          return diagonal({source: o, target: o});
        })
        .remove();

    // Stash the old positions for transition.
    nodes.forEach(function(d) {
      d.x0 = d.x;
      d.y0 = d.y;
    });
  }

  // Toggle children on click.
  function click(d) {
  	console.log(d)
    if (d.children) {
      d._children = d.children;
      d.children = null;
    } else {
      d.children = d._children;
      d._children = null;
    }
    update(d);
  }

  function has_child(d) {
  	return (d.children == null || d.children.length < 1 || d.children===undefined) && (d._children == null || d._children.length < 1 || d._children===undefined);
  }
}
