<?
  // open connection to mysql database
  include 'dbconnect.php';
?>

<html>
<head>
   <link rel="stylesheet" type="text/css" href="tabs.css"></link>
    <title>Bivio Test Center</title>

    <script type="text/javascript">
      function navto(inID) {
        var url=window.location.href;
        var header = url.split('?nav=',1);
        window.location = header + '?nav=' + inID;
      }
    </script>
</head>
<body>

<form action="" method="post">

<table width=90% align=center valign=top border=0 rules=none cellpadding=0 cellspacing=0>

<tr><td>

	<ul id="primary">
		<li><a href="index.html">Test Results</a></li>
		<li><a href="index.html">Test Status</a></li>
		<li><a href="portfolio.html">Start Test</a></li>
		<li><a href="contact.html">Testcase Info</a></li>
		<li><a href="about.html" class="current">Add Testbed</a></li>

	</ul>

</td></tr>
<tr><td>

	<div id="main">
	<ul id="secondary">
		<li><a href="philosophy.html">Our Philosophy</a></li>
		<li><span>Employment Opportunities</span></li>
		<li><a href="processes.html">Our Processes</a></li>
		<li><a href="quote.html">Get a Quote</a></li>
	</ul>
	</div>

</td></tr>
<tr><td>
		<div id="contents">

<?php

      include 'monitor.php';
?>
			<h2>Employment Opportunities</h2>			
			<p class="note">Sed purus neque, suscipit vitae, cursus vitae, porttitor non, dui. Mauris volutpat dui vitae sapien.</p>
			<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Curabitur viverra ultrices ante. Aliquam nec lectus. Praesent vitae risus. Aenean vulputate sapien et leo. Nullam euismod tortor id wisi. Sed facilisis, augue in ultrices fringilla, purus nisl euismod nibh, a placerat lacus quam sed elit.</p>
			<p>Sed purus neque, suscipit vitae, cursus vitae, porttitor non, dui. Mauris volutpat dui vitae sapien. Duis laoreet nibh vitae sem. Phasellus ornare. Morbi sollicitudin mi ut nibh. Morbi egestas elementum tellus.</p>

			<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Curabitur viverra ultrices ante. Aliquam nec lectus. Praesent vitae risus. Aenean vulputate sapien et leo. Nullam euismod tortor id wisi. Sed facilisis, augue in ultrices fringilla, purus nisl euismod nibh, a placerat lacus quam sed elit.</p>
			<p>Sed purus neque, suscipit vitae, cursus vitae, porttitor non, dui. Mauris volutpat dui vitae sapien. Duis laoreet nibh vitae sem. Phasellus ornare. Morbi sollicitudin mi ut nibh. Morbi egestas elementum tellus.</p>
			<p>Suspendisse magna dui, porta in, condimentum at, molestie nec, augue. Quisque vulputate facilisis ipsum.  Aenean sollicitudin quam sed ante. Donec at nunc. In hac habitasse platea dictumst. Suspendisse quis lorem sit amet eros congue volutpat. Nam laoreet ultricies pede. Nulla vestibulum, pede eget varius vestibulum, nisl mi aliquet nisl, eget eleifend quam dui faucibus tortor. Maecenas justo. In lacus nisl, tempus at, aliquam nec, ornare in, metus. Maecenas hendrerit mauris vitae purus. Cras id sem.</p>
			<p>Curabitur vel urna vitae nunc bibendum porttitor. Nam tortor quam, luctus id, convallis sed, rutrum ac, ante. Proin euismod lacus vitae elit. Nullam vel diam in metus consectetuer facilisis.</p>
			<p>In mauris enim, suscipit a, consequat quis, porta ut, diam. Vivamus tempor. Donec nec enim quis ante ullamcorper mollis. Praesent dictum. Donec arcu arcu, tincidunt a, placerat sit amet, porta eget, erat. Aliquam erat volutpat. Aenean egestas, dolor ut consectetuer pulvinar, mauris ante volutpat leo, non pulvinar erat justo vitae mauris. Donec laoreet dui at quam. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Mauris id libero. Morbi luctus sapien vitae dolor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nullam pharetra vestibulum leo. Maecenas magna velit, porta eu, viverra quis, cursus non, sapien.</p>
		</div>






</td></tr>
</table>

</form>

</body>
</html>
