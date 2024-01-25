<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="text-gray-400">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">{{ $title }}</h2>
                <form method="POST" >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to delete this meeting?')">Delete Meeting</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
