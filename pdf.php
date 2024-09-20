<?php
require('./fpdf/fpdf.php');

// ตั้งค่าขนาดกระดาษเป็น 10.2 x 29.7 เซนติเมตร
$pdf = new FPDF('P', 'cm', array(10.2, 29.7));
$pdf->AddPage();

// เพิ่มฟอนต์ Sarabun ที่สร้างจาก MakeFont (ตัวหนา)
$pdf->AddFont('Sarabun-Bold', '', 'Sarabun-Bold.php');
$pdf->AddFont('Sarabun-Thin', '', 'Sarabun-Thin.php');
// ตั้งค่าฟอนต์ Sarabun-Bold ขนาด 16 และจัดตำแหน่งข้อความให้อยู่ตรงกลาง
$pdf->SetFont('Sarabun-Bold', '', 16);

// ความกว้างของหน้ากระดาษ
$width = $pdf->GetPageWidth();

// ใช้ Cell เพื่อจัดข้อความให้อยู่ตรงกลาง (alignment 'C') และแปลงข้อความ UTF-8 เป็น TIS-620
$pdf->Cell($width - 2, 0, iconv('UTF-8', 'TIS-620', 'ข้าวมันไก่น้องนัน!'), 0, 1, 'C');

$pdf->SetFont('Sarabun-Thin', '', 12);
$pdf->Cell(0, 2, iconv('UTF-8', 'TIS-620', 'รายละเอียดคำสั่งซื้อ'), 0, 1, 'C');
$pdf->Cell(0, 2, 'Order ID: ' , 0, 1);
$pdf->Cell(0, 2, 'User: ' , 0, 1);
$pdf->Cell(0, 1, iconv('UTF-8', 'TIS-620', 'เลขโต๊ะ:') , 0, 1);
$pdf->Cell(0, 1, iconv('UTF-8', 'TIS-620', 'ลำดัคิว'), 0, 1);
$pdf->Cell(0, 2, iconv('UTF-8', 'TIS-620', 'รายละเอียดคำสั่งซื้อ'), 0, 1, 'C');
// สร้าง PDF
$pdf->Output();
?>
