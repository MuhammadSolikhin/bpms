<?php
// Cek jika form telah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $apt_number = $_POST['apt_number'];

    // Koneksi database
    include('includes/dbconnection.php');

    // Query untuk mencari appointment berdasarkan AptNumber
    $stmt = $con->prepare("SELECT * FROM tblappointment WHERE AptNumber = ?");
    $stmt->bind_param("i", $apt_number);
    $stmt->execute();
    $result = $stmt->get_result();

    // Menyiapkan response
    $response = array();

    if ($result->num_rows > 0) {
        // Ambil data appointment
        $appointment = $result->fetch_assoc();
        $status = $appointment['Status'];

        // Menyiapkan response sesuai status
        $response['success'] = true;
        $response['status'] = $status;
    } else {
        $response['success'] = false;
    }

    // Mengirim response dalam format JSON
    echo json_encode($response);
}
?>