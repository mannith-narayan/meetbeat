<x-app-layout>
    <style>
        body {
            overflow-y: hidden;
        }
    </style>
    <body>
        <div class="flex flex-col items-center justify-center h-screen">
            <h1 class="text-4xl text-gray-700 mb-6">No Meetings Yet</h1>
            <p class="text-gray-500 mb-6">Looks like there are no scheduled meetings at the moment. You can create a new one!</p>

            <button onclick="window.location.href='/meetings/create'"
                class="bg-blue-500 hover:bg-blue-700 text-white text-base font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline-blue">
                Add Meeting
            </button>
        </div>
    </body>
</x-app-layout>
