<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invite Link Expired - Chat System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a202c;
            color: #e2e8f0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .lock-animation {
            animation: shake 0.82s cubic-bezier(.36, .07, .19, .97) both;
            transform: translate3d(0, 0, 0);
        }

        @keyframes shake {

            10%,
            90% {
                transform: translate3d(-1px, 0, 0);
            }

            20%,
            80% {
                transform: translate3d(2px, 0, 0);
            }

            30%,
            50%,
            70% {
                transform: translate3d(-4px, 0, 0);
            }

            40%,
            60% {
                transform: translate3d(4px, 0, 0);
            }
        }
    </style>
</head>

<body>
    <div class="min-h-screen flex flex-col items-center justify-center px-4">
        <div class="max-w-md w-full bg-gray-800 rounded-lg shadow-xl overflow-hidden">
            <div class="bg-gray-900 px-6 py-4 border-b border-gray-700 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6 text-orange-500 mr-3">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
                <h2 class="text-xl font-bold">Invite Link Expired</h2>
            </div>
            <div class="p-6">
                <div class="flex justify-center my-6">
                    <div class="lock-animation bg-gray-700 p-5 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-16 h-16 text-orange-500">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                </div>

                <p class="mb-4">This invite link is not valid for registration in the system.</p>

                <p class="mb-6">Please contact the system administrator to obtain a valid invite link.</p>

            </div>
        </div>
        <div class="mt-6 text-sm text-gray-500">
            &copy; {{ date('Y') }} Chat System. All rights reserved.
        </div>
    </div>
</body>

</html>