<?php

/* 
 * Copyright (C) 2015 juacas
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

    require_once("../../../config.php");
    require_once("$CFG->dirroot/mod/assign/lib.php");
    require_once("locallib.php");
    require_once($CFG->libdir.'/gradelib.php');
    require_once ($CFG->dirroot.'/grade/lib.php');
    
    
    

// Get the params ----------------------------------------------------------------
    global $DB, $PAGE, $OUTPUT;
    $id    = required_param('id', PARAM_INT); // blended Course Module ID
   
   
        if (! $cm = get_coursemodule_from_id('blended', $id)){
           print_error("Course Module ID was incorrect");
        }    
        if (! $course = get_course($cm->course)) {
            print_error("Course is misconfigured");
        }    
        if (! $blended = $DB->get_record('blended',array('id'=> $cm->instance))) {
            print_error("Course module is incorrect");
        }
   

// Log ---------------------------------------------------------------------------

//    add_to_log($course->id, "blended", "grades", "grades.php?a=$blended->id", "$blended->id");

// Capabilities ----------------------------------------------------- 
        
    //require_login($course->id);
    require_login($cm->course, false,$cm);
    
    $context_course = context_course::instance($cm->course);
     // show headings and menus of page
    $url =  new moodle_url('/mod/blended/teams/teams_graph.php',array('id'=>$id));
    $PAGE->set_url($url);
    $PAGE->set_title(format_string($blended->name));
    $PAGE->set_heading($course->fullname);
    $PAGE->set_pagelayout('standard');
    
    $PAGE->navbar->add('graphs');
//    $PAGE->requires->js('http://d3js.org/d3.v3.min.js');
 
    $PAGE->requires->css(new moodle_url('/mod/blended/teams/teams_graph.css'));
    echo $OUTPUT->header();
    
//    list($students,$nonstudents,$active,$users)=  blended_get_users_by_type($context_course);
    $relations=array();
    $groups = groups_get_all_groups($course->id);
    foreach ($groups as $group){
        $members = groups_get_members($group->id);
        foreach ($members as $member){
            if (isset($relations[$member->id])){
                $member_entry =$relations[$member->id];
            }else{
                $member_entry = array();
                $member_entry["teamedwith"]=array();
                $member_entry["teamedwithnames"]=array();
                $member_entry["name"] = fullname($member);
            }
            
            $member_entry["teamedwith"]+=$members;
            unset($member_entry["teamedwith"][$member->id]);
            foreach ($member_entry["teamedwith"] as $user){
//                        $userpic = $OUTPUT->user_picture($user);
//                        $profilelink = '<a href="' . $CFG->wwwroot . '/user/view.php?id=' . $user->id . '&course=' . $course->id . '">' . fullname($user, true) . '</a>';

                $member_entry["teamedwithnames"][]= fullname($user);
            }
            $relations[$member->id]=$member_entry;
        }
    }
    // clean data
    $cleaned = array();
    foreach ($relations as $rel){
        unset($rel["teamedwith"]);
        $cleaned[]=$rel;
    }
    $jsonvar= json_encode($cleaned);
 echo $OUTPUT->container('','',"teams_graph");
echo $OUTPUT->footer();
    ?>
<script src="http://d3js.org/d3.v3.min.js"></script>
<script>
var classes = <?php echo $jsonvar; ?>;

var diameter = 960,
    radius = diameter / 2,
    innerRadius = radius - 120;

var cluster = d3.layout.cluster()
    .size([360, innerRadius])
    .sort(null)
    .value(function(d) { return d.size; });

var bundle = d3.layout.bundle();

var line = d3.svg.line.radial()
    .interpolate("bundle")
    .tension(.85)
    .radius(function(d) { return d.y; })
    .angle(function(d) { return d.x / 180 * Math.PI; });

var svg = d3.select("#teams_graph").append("svg")
    .attr("width", diameter)
    .attr("height", diameter)
  .append("g")
    .attr("transform", "translate(" + radius + "," + radius + ")");


  var nodes = cluster.nodes(packageHierarchy(classes)),
      links = packageImports(nodes);

  svg.selectAll(".link")
      .data(bundle(links))
    .enter().append("path")
      .attr("class", "link")
      .attr("d", line);

  svg.selectAll(".node")
      .data(nodes.filter(function(n) { return !n.children; }))
    .enter().append("g")
      .attr("class", "node")
      .attr("transform", function(d) { return "rotate(" + (d.x - 90) + ")translate(" + d.y + ")"; })
    .append("text")
      .attr("dx", function(d) { return d.x < 180 ? 8 : -8; })
      .attr("dy", ".31em")
      .attr("text-anchor", function(d) { return d.x < 180 ? "start" : "end"; })
      .attr("transform", function(d) { return d.x < 180 ? null : "rotate(180)"; })
      .text(function(d) { return d.key; });

d3.select(self.frameElement).style("height", diameter + "px");

// Lazily construct the package hierarchy from class names.
function packageHierarchy(classes) {
  var map = {};

  function find(name, data) {
    var node = map[name], i;
    if (!node) {
      node = map[name] = data || {name: name, children: []};
      if (name.length) {
        node.parent = find(name.substring(0, i = name.lastIndexOf(".")));
        node.parent.children.push(node);
        node.key = name.substring(i + 1);
      }
    }
    return node;
  }

//  classes.forEach(function(d) {
//    find(d.name, d);
//  });
 for (var d in classes) {
    find(classes[d].name, classes[d]);
  }

  return map[""];
}

// Return a list of imports for the given array of nodes.
function packageImports(nodes) {
  var map = {},
      imports = [];

  // Compute a map from name to node.
  nodes.forEach(function(d) {
    map[d.name] = d;
  });

  // For each import, construct a link from the source to target node.
  nodes.forEach(function(d) {
    if (d.teamedwithnames) d.teamedwithnames.forEach(function(i) {
      imports.push({source: map[d.name], target: map[i]});
    });
  });

  return imports;
}

</script>
