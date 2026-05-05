<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ForgotPasswordController extends Controller
{
    // Step 1: Send OTP to Email (Existing Logic Kept)
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $otp = rand(100000, 999999);

        DB::table('otps')->updateOrInsert(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'expires_at' => now()->addMinutes(10),
                'created_at' => now()
            ]
        );

        return $this->sendEmail($request->email, $otp);
    }

    private function sendEmail($email, $otp)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'rmajumdar214@gmail.com'; 
            $mail->Password   = 'xlhw mgaj zult apmi';  
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
            $mail->Port       = 465; 

            $mail->setFrom('rmajumdar214@gmail.com', 'AI_LEAD_SCORING_CRM');
            $mail->addAddress($email); 

            $mail->isHTML(true);
            $mail->Subject = 'Your Password Reset OTP';
            $mail->Body    = "Your OTP for password reset is: <b>$otp</b>. It expires in 10 minutes.";

            $mail->send();
            return back()->with('success', 'OTP sent to your email.');
        } catch (Exception $e) {
            return back()->withErrors(['message' => "Mail error: {$mail->ErrorInfo}"]);
        }
    }

    // Step 2: Verify OTP and Reset Password (Necessary Modifications Added)
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
            'password' => 'required|min:6|confirmed' // MODIFICATION: Added 'confirmed'
        ]);

        $record = DB::table('otps')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP. Please try again.']);
        }

        // --- NECESSARY MODIFICATION: Check if new password is same as old ---
        $user = DB::table('users')->where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'New password cannot be the same as your old password.']);
        }
        // --------------------------------------------------------------------

        DB::table('users')->where('email', $request->email)->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('otps')->where('email', $request->email)->delete();
        
        return redirect('/user/login')->with('success', 'Password reset successful!');
    }
}