<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="shadow-sm ">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-2xl font-bold mb-4 text-gray-400 ">My Uploaded Meetings</h2>
                <a href="{{ route('meetings.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white text-base font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline-blue">
                    Add Meeting
                </a>
            </div>

                @if ($meetings->count() > 0)
                    <table class="min-w-full bg-gray-600 border border-gray-200 text-gray-100">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">S.No.</th>
                                <th class="py-2 px-4 border-b">Title</th>
                                <th class="py-2 px-4 border-b">Date Created</th>
                                <th class="py-2 px-4 border-b">Time Created</th>
                                <th class="py-2 px-4 border-b">Transcript Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($meetings as $index => $meeting)
                                <tr class="text-center">
                                    <td class="py-2 px-4 border-b">{{ $index + 1 }}</td>
                                    <td class="py-2 px-4 border-b">
                                        <!-- Wrap the entire row with an anchor tag -->
                                        <a href="{{ route('meetings.show', $meeting->id) }}">
                                            {{ $meeting->title }}
                                        </a>
                                    </td>
                                    <td class="py-2 px-4 border-b">{{ $meeting->created_at->format('M d, Y') }}</td>
                                    <td class="py-2 px-4 border-b">{{ $meeting->created_at->format('H:i A') }}</td>
                                    <td class="py-2 px-4 border-b">Not Complete</td>
                                </tr>
                            
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500">You have no scheduled meetings.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
