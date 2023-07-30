<?php
session_start();

// If the user is not logged in, redirect back to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Logout feature
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Here you can include any other admin dashboard logic
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://d3js.org/d3.v7.min.js"></script>
</head>
<!-- ... (rest of the HTML) -->
<body>
    <div class="container">
        <div id="myTree"></div>
        <h1 class="text-center mt-5">Welcome to Admin Dashboard</h1>
        <p>Hello, <?php echo $_SESSION['username']; ?>!</p>
        <!-- Add dashboard content here -->
        <a href="dashboard.php?logout=1" class="btn btn-primary">Logout</a>
        <div id="tree"></div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <style>
        svg{
    display: block;
    margin: auto;
}

.node circle{
    fill: #fff;
    stroke: steelblue;
    stroke-width: 3px;
}

.node text{ font: 12px sans-serif; }

.link{
    fill: none;
    stroke: #ccc;
    stroke-width: 2px;
}
    </style>
</body>

<script>
  function drawTree(treeData)   {
    console.log(treeData);
  
console.log(treeData);

// Set dimensions and margins for diagram
var margin = {top: 80, bottom: 80},
    width = 600,
    height = 400 - margin.top - margin.bottom;
    
// append the svg object to the body of the page
// appends a 'group' element to 'svg'
// moves the 'group' element to the top left margin   
var svg = d3.select("body").append("svg")
    .attr("width", "100%")
    .attr("height", "100%")//height + margin.top + margin.bottom)
    .attr("viewBox","0 0 600 350")
    .append("g")
    .attr("transform", "translate(0," + margin.top + ")");
                
var i = 0,      
    duration = 750,
    root;

// Declares a tree layout and assigns the size
var treemap = d3.tree().size([width, height]);

// Assigns parent, children, height, depth
root = d3.hierarchy(treeData, function(d){ 
    console.log(d.children)
    return d.children; 
});

root.x0 = width / 2;
root.y0 = 0;


update(root);

// Collapse the node and all it's children
function collapse(d){
    if(d.children){
        d._children = d.children
        d._children.forEach(collapse)
        d.children = null;
    }
}

// Update
function update(source){
    // Assigns the x and y position for the nodes
    var treeData = treemap(root);
    
    // Compute the new tree layout.
    var nodes = treeData.descendants(),
        links = treeData.descendants().slice(1);
        
    // Normalize for fixed-depth
    nodes.forEach(function(d){ d.y = d.depth*100 });    
    
    // **************** Nodes Section ****************
    
    // Update the nodes...
    var node = svg.selectAll('g.node')
         .data(nodes, function(d) {return d.id || (d.id = ++i); });
         
    // Enter any new nodes at the parent's previous position.
    var nodeEnter = node.enter().append('g')
                     .attr('class', 'node')
                     .attr("transform", function(d){
                         return "translate(" + source.x0 + "," + source.y0 + ")";
                     })
                     .on('click', click);
                     
    // Add Circle for the nodes
    nodeEnter.append('circle')
        .attr('class', 'node')
        .attr('r', 1e-6)
        .style("fill", function(d){
            return d._children ? "lightsteelblue" : "#fff";
        });
    
    // Add labels for the nodes
    nodeEnter.append('text')
        .attr("dy", ".35em")
        .attr("x", function(d){
            return d.children || d._children ? -13 : 13;
        })
        .attr("text-anchor", function(d){
            return d.children || d._children ? "end" : "start";
        })
        .text(function(d){ return d.data.name; });
    
    // Update
    var nodeUpdate = nodeEnter.merge(node);
    
    // Transition to the proper position for the nodes
    nodeUpdate.transition()
        .duration(duration)
        .attr("transform", function(d) {
            return "translate(" + d.x + "," + d.y + ")";
        });
    
    // Update the node attributes and style
    nodeUpdate.select('circle.node')
        .attr('r', 10)
        .style("fill", function(d){
            return d._children ? "lightsteelblue" : "#fff";
        })
        .attr('cursor', 'pointer');
        
    // Remove any exiting nodes
    nodeExit = node.exit().transition()
        .duration(duration)
        .attr("transform", function(d){
            return "translate(" + source.x +","+ source.y +")";
        })
        .remove();
        
    // On exit reduce the node circles size to 0
    nodeExit.select('circle')
        .attr('r', 1e-6);
    
    // On exit reduce the opacity of text lables  
    nodeExit.select('text')
        .style('fill-opacity', 1e-6)
        
    // **************** Links Section ****************
    
    // Update the links...
    var link = svg.selectAll('path.link')
        .data(links, function(d){ return d.id; });
        
    // Enter any new links at the parent's previous position
    var linkEnter = link.enter().insert('path', "g")
        .attr("class", "link")
        .attr('d', function(d){
            var o = {x: source.x0, y: source.y0};
            return diagonal(o,o);
        });
    
    // Update
    var linkUpdate = linkEnter.merge(link);
    
    // Transition back to the parent element position
    linkUpdate.transition()
        .duration(duration)
        .attr('d', function(d){ return diagonal(d, d.parent) });
    
    // Remove any existing links
        var linkExit = link.exit().transition()
            .duration(duration)
            .attr('d', function(d){
                var o = {x: source.x, y: source.y};
            })
            .remove();
    
    // Store the old positions for transition.
    nodes.forEach(function(d){
        d.x0 = d.x;
        d.y0 = d.y;
    });
    
    // Create a curved (diagonal) path from parent to the child nodes
    function diagonal(s,d){
        path = `M ${s.x} ${s.y}
        C ${(s.x + d.x) / 2} ${s.y},
          ${(s.x + d.x) / 2} ${d.y},
          ${d.x} ${d.y}`

        return path;
    }

    // Toggle children on click
    function click(d){
        if (d.children){
            d._children = d.children;
            d.children = null;
        }
        else{
            d.children = d._children;
            d._children = null;
        }
        update(d);
    }
}
}

        document.addEventListener("DOMContentLoaded", function() {
            fetch('get_user_hierarchy.php')
                .then(response => response.json())
                .then(treeData => {
                    // Call the function to draw the tree structure
                    drawTree(treeData);
                })
                .catch(error => {
                    console.error('Error fetching user hierarchy data:', error);
                });
        });
    </script>
    </script>

</html>
