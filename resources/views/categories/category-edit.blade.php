<x-layout>
<div class="simple-text">
    <table class="item-container">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <div class="category-admin">
                @if ($category)
                    <tr class="category-item">
                        <form method="POST" action="{{ route('category.update', $category) }}">
                            
                            @csrf
                            @method('PUT')

                            <td> {{ $category->id }}</td>
                            <td> 
                                <input type="text" name="title" id="title" value="{{ old('title') ?? $category->title }}" required/>
                            </td>
                            <td>
                                <input type="text" name="description" id="description" value="{{ old('description') ?? $category->description }}" /> 
                            </td>
                            <td>
                                <button>Update</button>
                            </td>
                        </form>
                    </tr>
                @endif
            </div>
        </tbody>
    </table>
</div>
</x-layout>