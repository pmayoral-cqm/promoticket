<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

function Send_Mail($to, $subject, $body)
{
    try {
        $from = "noreply@emotivoo.com"; // Replace with your verified sender
        $mail = new PHPMailer();

        $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_CONNECTION; // Enable debug output
        $mail->isSMTP(); // Use SMTP
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use TLS encryption
        $mail->Host = "email-smtp.eu-west-1.amazonaws.com"; // Amazon SES host
        $mail->Port = 587; // Port (try 24 or 2587 if necessary)
        $mail->Username = "AKIA3Z73GW3EKOTOKTND"; // Correct Smtp Username
        $mail->Password = "BNi+4ywrCZ/YJ6JrG11L92m2YbGZ6sbnVCSNKAYyfvJ7"; // Correct Smtp Password

        $mail->SetFrom($from, 'Emotivoo SEM');
        $mail->AddReplyTo($from, 'noreply@emotivoo.com');

        $mail->isHTML(true); // Email format to HTML
        $mail->Body = $body;
        $mail->Subject = $subject;

        $mail->AddAddress($to); // Add recipient
        if (!$mail->send()) {
            echo $mail->ErrorInfo;
            return false;
        }
        
        return true;
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve form data and sanitize inputs
    $nombre = htmlspecialchars(trim($_POST["nombre"])); // Name field
    $email = htmlspecialchars(trim($_POST["email"]));   // Email field
    $telefono = htmlspecialchars(trim($_POST["telefono"])); // Phone field
    $mensaje = htmlspecialchars(trim($_POST["mensaje"]));   // Message field

    // Check required fields
    if (empty($nombre) || empty($email) || empty($telefono)) {
        // Redirect if fields are missing
        header("Location: https://emotivoo.com/promo/fidelizacion?error=empty_fields");
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: https://emotivoo.com/promo/fidelizacion?error=invalid_email");
        exit();
    }

    // Validate phone format (only numbers)
    if (!preg_match('/^[0-9]+$/', $telefono)) {
        header("Location: https://emotivoo.com/promo/fidelizacion?error=invalid_phone");
        exit();
    }

    // Recipient email address
    $destinatario = "pmayoral@chequemotiva.com";

    // Email subject
    $asunto = "Emotivoo: Contacto desde campaña Ads";

    // Email body content (with image, introduction, and formatted fields)
    $cuerpo = "<div style='font-family: Arial, sans-serif; line-height: 1.5;'>";
    $cuerpo .= "<img style='width: 150px; height: auto; margin-top: 15px;' src='https://www.emotivoo.com/wp-content/uploads/2023/03/MicrosoftTeams-image-16.png' alt='Logo'>";
    $cuerpo .= "<p>Un usuario ha registrado sus datos en nuestra landing de campaña de pago. Estos son sus datos para contacto:</p>";
    $cuerpo .= "<p><b>Nombre:</b> $nombre<br>";
    $cuerpo .= "<b>Email:</b> $email<br>";
    $cuerpo .= "<b>Teléfono:</b> $telefono<br>";
    if (!empty($mensaje)) {
        $cuerpo .= "<b>Mensaje:</b> $mensaje<br>";
    }
    $cuerpo .= "</p>";
    $cuerpo .= "</div>";

    // Send the email
    if (Send_Mail($destinatario, $asunto, $cuerpo)) {
        // Redirect on successful send
        header("Location: https://emotivoo.com/"); // Replace with your success page
        exit();
    } else {
        // Redirect on failure to send
        header("Location: https://emotivoo.com/promo/fidelizacion?error=mail_failed");
        exit();
    }
} else {
    // Redirect if accessed directly
    header("Location: https://emotivoo.com/promo/fidelizacion");
    exit();
}


?>






