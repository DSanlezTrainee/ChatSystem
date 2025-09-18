<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro - Chat System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a202c;
            color: #e2e8f0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex flex-col items-center justify-center px-4">
        <div class="max-w-md w-full bg-gray-800 rounded-lg shadow-xl overflow-hidden">
            <div class="bg-gray-900 px-6 py-4 border-b border-gray-700 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-500 mr-3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
                <h2 class="text-xl font-bold">Oops! Something's wrong</h2>
            </div>
            <div class="p-6">
                <div class="flex justify-center my-6">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-20 h-20 text-red-500 opacity-70">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>

                <p class="mb-4">Sorry, we encountered an unexpected error while processing your request.</p>

                <p class="mb-6">Please try again later or contact the system administrator for assistance.</p>

            </div>
        </div>
        <div class="mt-6 text-sm text-gray-500">
            &copy; {{ date('Y') }} Chat System. Todos os direitos reservados.
        </div>
    </div>
</body>
</html>