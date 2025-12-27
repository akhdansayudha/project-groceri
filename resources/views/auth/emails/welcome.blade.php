<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Vektora</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap');

        body {
            font-family: 'Manrope', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #F3F4F6;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            color: #111111;
        }

        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                padding: 20px !important;
            }

            .content-padding {
                padding: 30px 20px !important;
            }

            .heading {
                font-size: 24px !important;
            }
        }
    </style>
</head>

<body style="background-color: #F3F4F6; margin: 0; padding: 40px 0;">

    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">

                <table class="email-container" role="presentation" width="500" border="0" cellspacing="0"
                    cellpadding="0"
                    style="background-color: #ffffff; border-radius: 32px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.05); margin: 0 auto;">

                    {{-- HEADER LOGO --}}
                    <tr>
                        <td align="center" style="padding: 40px 0 20px 0;">
                            <a href="{{ route('home') }}" style="text-decoration: none; display: inline-block;">
                                <span
                                    style="font-family: 'Manrope', sans-serif; font-size: 28px; font-weight: 800; color: #000000; letter-spacing: -1px;">
                                    vektora<span style="color: #2563EB;">.</span>
                                </span>
                            </a>
                        </td>
                    </tr>

                    {{-- HERO ICON (Rocket / Star) --}}
                    <tr>
                        <td align="center" style="padding: 0 40px;">
                            <div
                                style="background-color: #F0F9FF; border-radius: 20px; padding: 20px; display: inline-block;">
                                {{-- Icon Rocket --}}
                                <img src="https://cdn-icons-png.flaticon.com/512/1067/1067357.png" alt="Rocket Icon"
                                    width="48" style="display: block; opacity: 1;">
                            </div>
                        </td>
                    </tr>

                    {{-- MAIN CONTENT --}}
                    <tr>
                        <td class="content-padding" style="padding: 30px 50px 50px 50px; text-align: center;">

                            <h1 class="heading"
                                style="margin: 0 0 15px 0; font-size: 28px; font-weight: 700; color: #111111; letter-spacing: -0.5px;">
                                You're in! Welcome.
                            </h1>

                            <p style="margin: 0 0 30px 0; font-size: 15px; line-height: 1.6; color: #666666;">
                                Hi <strong>{{ $user->full_name }}</strong>, thank you for joining Vektora. <br><br>
                                We are thrilled to have you as our partner. Your dashboard is ready—start exploring our
                                creative services or manage your projects seamlessly.
                            </p>

                            {{-- CTA BUTTON --}}
                            <table role="presentation" border="0" cellspacing="0" cellpadding="0"
                                style="margin: 0 auto;">
                                <tr>
                                    <td align="center" style="border-radius: 50px; background-color: #000000;">
                                        <a href="{{ route('client.dashboard') }}" target="_blank"
                                            style="font-size: 15px; font-family: 'Manrope', sans-serif; font-weight: 600; color: #ffffff; text-decoration: none; padding: 16px 36px; border-radius: 50px; display: inline-block; border: 1px solid #000000;">
                                            Go to Dashboard
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 30px 0 0 0; font-size: 13px; line-height: 1.6; color: #9CA3AF;">
                                Need help getting started? Simply reply to this email, and our team will get back to
                                you.
                            </p>

                        </td>
                    </tr>

                    <tr>
                        <td style="border-top: 1px solid #F3F4F6;"></td>
                    </tr>

                    {{-- FOOTER --}}
                    <tr>
                        <td style="padding: 30px; text-align: center; background-color: #FAFAFA;">
                            <p style="margin: 0; font-size: 12px; color: #9CA3AF; font-weight: 500;">
                                &copy; {{ date('Y') }} Vektora Creative Agency.
                            </p>
                            <div style="margin-top: 10px;">
                                <a href="#"
                                    style="font-size: 12px; color: #9CA3AF; text-decoration: none; margin: 0 8px;">Instagram</a>
                                <span style="color: #E5E7EB;">|</span>
                                <a href="#"
                                    style="font-size: 12px; color: #9CA3AF; text-decoration: none; margin: 0 8px;">Website</a>
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>
