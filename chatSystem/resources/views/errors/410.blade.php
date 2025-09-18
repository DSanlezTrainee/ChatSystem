<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Expired - Chat System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a202c;
            color: #e2e8f0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .7;
            }
        }
    </style>
</head>

<body>
    <div class="min-h-screen flex flex-col items-center justify-center px-4">
        <div class="max-w-md w-full bg-gray-800 rounded-lg shadow-xl overflow-hidden">
            <div class="bg-gray-900 px-6 py-4 border-b border-gray-700 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6 text-red-500 mr-3">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                <h2 class="text-xl font-bold">Invite Link Expired</h2>
            </div>
            <div class="p-6">
                <div class="animate-pulse bg-red-900 bg-opacity-20 rounded-md p-4 mb-6">
                    <p class="text-red-400">This invite link has expired and can no longer be used.</p>
                </div>

                <p class="mb-4">The system administrator has generated a new invite link, making this link invalid.</p>

                <p class="mb-6">Please request a new invite link from the system administrator to register.</p>


            </div>
        </div>
        <div class="mt-6 text-sm text-gray-500">
            &copy; {{ date('Y') }} Chat System. All rights reserved.
        </div>
    </div>
</body>

</html>