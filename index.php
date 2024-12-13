<?php
include('includes/dbconnection.php');
session_start();
error_reporting(0);

// Ambil data services dari database
$services = [];
$query_services = mysqli_query($con, "SELECT ID, ServiceName, Cost FROM tblservices");
while ($row = mysqli_fetch_assoc($query_services)) {
	$services[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$name = $_POST['name'];
	$email = $_POST['email'];
	$services_selected = $_POST['services'];
	$adate = $_POST['adate'];
	$atime = $_POST['atime'];
	$phone = $_POST['phone'];
	$aptnumber = mt_rand(100000000, 999999999);
	$applyDate = date('Y-m-d H:i:s');
	$remark = '';
	$status = 'pending';
	$remarkDate = null;

	// Simpan data ke tblappointment
	$stmt = $con->prepare("INSERT INTO tblappointment (AptNumber, Name, Email, PhoneNumber, AptDate, AptTime, ApplyDate, Remark, Status, RemarkDate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("isssssssss", $aptnumber, $name, $email, $phone, $adate, $atime, $applyDate, $remark, $status, $remarkDate);
	$stmt->execute();

	$appointment_id = $con->insert_id;

	// Simpan data ke detail_appointment
	foreach ($services_selected as $service_id) {
		$stmt = $con->prepare("INSERT INTO detail_appointment (appointment_id, service_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
		$stmt->bind_param("ii", $appointment_id, $service_id);
		$stmt->execute();
	}

	header('Location:thank-you.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>BPMS||Home Page</title>

	<link href="https://fonts.googleapis.com/css?family=Work+Sans:100,200,300,400,500,600,700,800,900" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Pacifico" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,400i,700,700i,900,900i" rel="stylesheet">

	<link rel="stylesheet" href="css/open-iconic-bootstrap.min.css">
	<link rel="stylesheet" href="css/animate.css">

	<link rel="stylesheet" href="css/owl.carousel.min.css">
	<link rel="stylesheet" href="css/owl.theme.default.min.css">
	<link rel="stylesheet" href="css/magnific-popup.css">

	<link rel="stylesheet" href="css/aos.css">

	<link rel="stylesheet" href="css/ionicons.min.css">

	<link rel="stylesheet" href="css/bootstrap-datepicker.css">
	<link rel="stylesheet" href="css/jquery.timepicker.css">


	<link rel="stylesheet" href="css/flaticon.css">
	<link rel="stylesheet" href="css/icomoon.css">
	<link rel="stylesheet" href="css/style.css">
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
	<?php include_once('includes/header.php'); ?>
	<!-- END nav -->

	<section id="home-section" class="hero" style="background-image: url(images/1.jpg);"
		data-stellar-background-ratio="0.5">
		<div class="home-slider owl-carousel">
			<div class="slider-item js-fullheight">
				<div class="overlay"></div>
				<div class="container-fluid p-0">
					<div class="row d-md-flex no-gutters slider-text align-items-end justify-content-end"
						data-scrollax-parent="true">
						<img class="one-third align-self-end order-md-last img-fluid" src="images/bg_1.png" alt="">
						<div class="one-forth d-flex align-items-center ftco-animate"
							data-scrollax=" properties: { translateY: '70%' }">
							<div class="text mt-5">
								<span class="subheading">Salon Kecantikan</span>
								<h1 class="mb-4">Tampil Cantik</h1>
								<p class="mb-4">We pride ourselves on our high quality work and attention to detail. The
									products we use are of top quality branded products.</p>


							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="slider-item js-fullheight">
				<div class="overlay"></div>
				<div class="container-fluid p-0">
					<div class="row d-flex no-gutters slider-text align-items-center justify-content-end"
						data-scrollax-parent="true">
						<img class="one-third align-self-end order-md-last img-fluid" src="images/bg_2.png" alt="">
						<div class="one-forth d-flex align-items-center ftco-animate"
							data-scrollax=" properties: { translateY: '70%' }">
							<div class="text mt-5">
								<span class="subheading">Kecantikan natural</span>
								<h1 class="mb-4">Salon kecantikan</h1>
								<p class="mb-4">This parlour provides huge facilities with advanced technology
									equipments and best quality service. Here we offer best treatment that you might
									have never experienced before.</p>


							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>


	<br>
	<section class="ftco-section ftco-no-pt ftco-booking">
		<div class="container-fluid px-0">
			<div class="row no-gutters d-md-flex justify-content-end">
				<div class="one-forth d-flex align-items-end">
					<div class="text">
						<div class="overlay"></div>
						<div class="appointment-wrap">
							<span class="subheading">Reservasi</span>
							<h3 class="mb-2">Buat Perjanjian</h3>
							<form id="appointment-form" action="#" method="post" class="appointment-form">
								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<input type="text" class="form-control" id="name" placeholder="Name"
												name="name" required="true">
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<input type="email" class="form-control" id="appointment_email"
												placeholder="Email" name="email" required="true">
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<div class="select-wrap">
												<label for="">service</label>
												<select id="services" class="js-example-basic-multiple form-control"
													name="services[]" multiple required>
													<?php foreach ($services as $service): ?>
														<option value="<?= $service['ID'] ?>"><?= $service['ServiceName'] ?>
															(Rp <?= $service['Cost'] ?>)</option>
													<?php endforeach; ?>
												</select>
											</div>
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<input type="text" class="form-control appointment_date" placeholder="Date"
												name="adate" id='adate' required="true">
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<input type="text" class="form-control appointment_time" placeholder="Time"
												name="atime" id='atime' required="true">
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<input type="text" class="form-control" id="phone" name="phone"
												placeholder="Phone" required="true" maxlength="10" pattern="[0-9]+">
										</div>
									</div>
								</div>
								<div class="form-group">
									<input type="submit" name="appointment_submit" value="Make an Appointment"
										class="btn btn-primary">
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="one-third">
					<div class="img" style="background-image: url(images/bg-1.jpg);">
					</div>
				</div>
			</div>
		</div>
	</section>


	<br>


	<?php include_once('includes/footer.php'); ?>



	<!-- loader -->
	<div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px">
			<circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee" />
			<circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10"
				stroke="#F96D00" />
		</svg></div>


	<script src="js/jquery.min.js"></script>
	<script src="js/jquery-migrate-3.0.1.min.js"></script>
	<script src="js/popper.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.easing.1.3.js"></script>
	<script src="js/jquery.waypoints.min.js"></script>
	<script src="js/jquery.stellar.min.js"></script>
	<script src="js/owl.carousel.min.js"></script>
	<script src="js/jquery.magnific-popup.min.js"></script>
	<script src="js/aos.js"></script>
	<script src="js/jquery.animateNumber.min.js"></script>
	<script src="js/bootstrap-datepicker.js"></script>
	<script src="js/jquery.timepicker.min.js"></script>
	<script src="js/scrollax.min.js"></script>
	<script
		src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
	<script src="js/google-map.js"></script>
	<script src="js/main.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
	<script>
		$(document).ready(function () {
			$('.js-example-basic-multiple').select2();
		});
	</script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script>
		document.addEventListener("DOMContentLoaded", function () {
			// Define the service combos
			const serviceCombos = {
				"1": "2",
				"2": "1",
				"3": "4",
				"4": "3",
				"5": "6",
				"6": "5",
				"7": "8",
				"8": "7"
			};

			document.getElementById("appointment-form").addEventListener("submit", function (e) {
				// Get selected services
				const selectedServices = Array.from(document.querySelectorAll('[name="services[]"] option:checked')).map(option => option.value);

				// Find missing pairs
				let missingPair = null;
				selectedServices.forEach(serviceId => {
					const expectedPair = serviceCombos[serviceId];
					if (expectedPair && !selectedServices.includes(expectedPair)) {
						missingPair = expectedPair;
					}
				});

				if (missingPair) {
					e.preventDefault(); // Stop the form submission

					// Show SweetAlert suggestion
					Swal.fire({
						title: "Suggestion",
						text: `Do you want to add the paired service Fruit facial ?`,
						icon: "question",
						showCancelButton: true,
						confirmButtonText: "Yes, add it",
						cancelButtonText: "No, proceed",
					}).then((result) => {
						if (result.isConfirmed) {
							// Add the missing service to the selected options
							const missingOption = document.querySelector(`[name="services[]"] option[value="${missingPair}"]`);
							if (missingOption) {
								missingOption.selected = true;
							}
							// Re-submit the form
							document.getElementById("appointment-form").submit();
						}
					});
				}
			});
		});
	</script>



</body>

</html>