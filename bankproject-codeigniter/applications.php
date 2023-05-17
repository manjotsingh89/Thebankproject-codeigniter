<?php
include("_header.php");
?>
		<div class="header">
			<div class="row">
				<div class="col-6">
					<h2>My Applications</h2>
				</div>
			</div>

			<table class="table table-primary" id="table-my-interviews">
				<thead>
					<tr>
						<th class="sortable" data-order="DESC" data-column="ApplicationID"><div>ID<i class="arrow arrow-up active"></i></div></th>
						<th class="sortable" data-order="ASC" data-column="ApplicationID"><div>Full Name<i class="arrow arrow-up"></i></div></th>
						<th class="sortable" data-order="ASC" data-column="Email"><div>Email<i class="arrow arrow-up"></i></div></th>
						<th class="sortable" data-order="ASC" data-column="TelephoneNumber"><div>Telephone<i class="arrow arrow-up"></i></div></th>
						<th class="sortable" data-order="ASC" data-column="JobTitleName"><div>Job Applied to<i class="arrow arrow-up"></i></div></th>
						<th class="sortable" data-order="ASC" data-column="ApplicationStatus"><div>Status<i class="arrow arrow-up"></i></div></th>
						<th class="sortable" data-order="ASC" data-column="InterviewCount"><div>Interviewed<i class="arrow arrow-up"></i></div></th>
						<th></th>
		   		</thead>
				<tbody>
				</tbody>
			</table>
			<div class="text-center" id="my-interviews-loader" style="display: none;">
				<div id="loading"></div>
			</div>
		</div>

		<!-- Core theme JS-->
		<script type="text/javascript" src="js/jquery-3.6.0.min.js"></script>
		<script type="text/javascript" src="js/popper.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/datatables.js"></script>
		<script type="text/javascript" src="js/alertify.min.js"></script>
		<script type="text/javascript" src="js/incipit/incipit.js"></script>
		<script type="text/javascript" src="build/js/intlTelInput.js"></script> 
		<script type="text/javascript" src="js/scripts.js"></script>
		<script type="text/javascript" src="js/reviews.js"></script>
		<script type="text/javascript" src="js/select2.js"></script>
		<script type="text/javascript" src="js/jquery-validation-3.6.0.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				getApplications();
			});
		</script>
    </body>
</html>