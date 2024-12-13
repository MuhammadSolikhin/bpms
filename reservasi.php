<?php
include('includes/dbconnection.php');

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

    // Cek kondisi layanan
    if (in_array(1, $services_selected) && !in_array(2, $services_selected)) {
        // Tampilkan SweetAlert konfirmasi
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Konfirmasi Layanan',
                    text: 'Anda memilih layanan ID 1. Apakah Anda juga ingin memilih layanan ID 2 untuk melengkapi paket?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, tambahkan ID 2',
                    cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Tambahkan ID 2 ke form dan submit ulang
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '';
                        
                        // Tambahkan semua input yang sudah ada
                        const inputs = " . json_encode($_POST) . ";
                        for (const key in inputs) {
                            if (Array.isArray(inputs[key])) {
                                inputs[key].forEach(value => {
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = `${key}[]`;
                                    input.value = value;
                                    form.appendChild(input);
                                });
                            } else {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = key;
                                input.value = inputs[key];
                                form.appendChild(input);
                            }
                        }
                        
                        // Tambahkan ID 2
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'services[]';
                        hiddenInput.value = '2';
                        form.appendChild(hiddenInput);

                        document.body.appendChild(form);
                        form.submit();
                    } else {
                        Swal.fire('Layanan disimpan', 'Hanya ID 1 yang dipilih.', 'info');
                    }
                });
            });
        </script>";
        exit; // Hentikan eksekusi sebelum menyimpan ke database
    }

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

    echo "<script>
        Swal.fire('Berhasil!', 'Data appointment berhasil disimpan.', 'success')
            .then(() => { window.location = 'index.php'; });
    </script>";
}
?>