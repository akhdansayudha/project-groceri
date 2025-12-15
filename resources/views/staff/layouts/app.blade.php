<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Portal - Vektora</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    },
                    colors: {
                        bento: {
                            dark: '#050505',
                            /* Lebih gelap dari login */
                            card: '#111111',
                            border: '#222222',
                            accent: '#ffffff',
                            muted: '#737373',
                        }
                    },
                    borderRadius: {
                        'bento': '1rem'
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .glass-effect {
            background: rgba(17, 17, 17, 0.8);
            backdrop-filter: blur(12px);
        }

        /* Scrollbar Dark Mode */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #050505;
        }

        ::-webkit-scrollbar-thumb {
            background: #333;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>

<body class="bg-bento-dark text-gray-300 antialiased overflow-hidden">

    <div class="flex h-screen">
        @include('staff.partials.sidebar')

        <div class="flex-1 flex flex-col h-full overflow-hidden relative">

            @include('staff.partials.header')

            <main class="flex-1 overflow-y-auto p-6 md:p-8">
                @yield('content')
            </main>

        </div>
    </div>

    <script>
        feather.replace();
    </script>
</body>

</html>
