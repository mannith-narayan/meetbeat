<x-app-layout>
    <div class="py-20">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 font-bold text-2xl">
                    <div class = "mt-2 flex justify-center items-center">Create a Meeting</div>

                    <form method="post" action="{{ URL :: route("meetings.store") }}" class="max-w-md mx-auto shadow-md p-8 mt-2" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label 
                            for="meeting-title" 
                            class="block text-gray-900 dark:text-gray-100 text-lg font-bold mb-2"> Title </label>
                            <input
                                type="text"
                                id="meeting-title"
                                name="meeting-title"
                                class="w-full px-3 py-2 border rounded-md text-gray-700 focus:outline-none focus:border-blue-500
                                border @error('meeting-title') border-red-500 @enderror"
                                placeholder="Enter meeting title"
                            >
                        </div>
                        
                        <div class="mb-4">
                            <label for="meeting-desc" class="block text-gray-900 dark:text-gray-100 text-lg font-bold mb-2"> Description </label>
                            <textarea
                                id="meeting-desc"
                                name="meeting-desc"
                                class="w-full px-3 py-2 border rounded-md text-gray-700 focus:outline-none focus:border-blue-500
                                border @error('meeting-desc') border-red-500 @enderror"
                                placeholder="Enter meeting description"
                                rows="4"
                                ></textarea>
                        </div>

                        <div class= "mb-4">
                            
                            <label for="audio-file" class="block text-gray-900 dark:text-gray-100 text-lg font-bold mb-2"> Upload Audio File </label>
                            <input 
                            name = "audio-file"
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg 
                            cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 
                            dark:border-gray-600 dark:placeholder-gray-400 border @error('audio-file') border-red-500 @enderror"
                            aria-describedby="audio-file" 
                            id="audio-file" 
                            type="file">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">M4A,MP3,MP4 or WAV </p>

                        </div>

                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li class="text-left text-sm text-red-500">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="flex items-center justify-end">
                            <button
                                type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white text-base font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline-blue"
                            >
                                Upload
                            </button>
                        </div>
                      </form>
                      
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

