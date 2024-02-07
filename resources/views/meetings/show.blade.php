<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="text-gray-400">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-4xl font-bold">{{ $meeting->title }}</h2>
                <form method="POST" >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to delete this meeting?')">Delete Meeting</button>
                </form>
            </div>

            <br>
            <style>
                h2 {
                    font-weight: bold; 
                    font-size: 1.5em;
                }
            
                ul {
                    margin-bottom: 10px;
                    list-style-type: circle;
                }
            
                li {
                    font-size: 1em;
                }
            </style>

            <div>{!! $meeting->summary !!}</div>
        </div>
    </div>
</x-app-layout>
