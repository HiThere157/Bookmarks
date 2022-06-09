@extends('layouts.default')

@section('content')
    <div class="d-flex justify-content-between align-items-center mx-5">
        <h1 class="mt-3">Saved Bookmarks</h1>
    </div>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th style="width: 0;">Id</th>
                <th>Title</th>
                <th>URL</th>
                <th>Created By</th>
                <th>Updated At</th>
                <th style="width: 11rem;">Actions</th>
            </tr>
        </thead>
        <tbody>

            <form action="{{ route('bookmarks.create') }}" method="post">
                @csrf
                <tr>
                    <td></td>
                    <td>
                        <input type="text" class="form-control" name="title" spellcheck="false" />
                    </td>
                    <td>
                        <input type="text" class="form-control" name="url" spellcheck="false" />
                    </td>
                    <td></td>
                    <td></td>
                    <td class="text-end">
                        <button type="submit" class="btn btn-primary">Create</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                    </td>
                </tr>
            </form>

            @foreach($bookmarks as $bookmark)
                <tr>
                    <td>{{ $bookmark->id }}</td>
                    <td>{{ $bookmark->title }}</td>
                    <td>{{ $bookmark->url }}</td>
                    <td>{{ $bookmark->created_by->username }}</td>
                    <td>{{ $bookmark->updated_at }}</td>
                    <td class="text-end">
                        <button name="openEditBookmarkModal" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editBookmarkModal" data-bs-editId="{{ $bookmark->id }}">Edit</button>
                        <button name="openDeleteModal" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-bs-deleteId="{{ $bookmark->id }}">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <x-confirm-delete-modal />
    <x-edit-bookmark-modal />

    <script>
        $(document).ready(function() {
            //set confirm button to correct route
            $('[name="openDeleteModal"]').click(function() {
                $('#deleteModalHeader').text('Delete Bookmark');
                $('#deleteModalBody').text('Are you sure you want to delete this bookmark?');
                $('#deleteModalConfirm').prop('href', "{{ route('bookmarks.delete', ':id')}}".replace(':id', $(this).attr('data-bs-deleteId')));
            });

            //set form action to correct route
            $('[name="openEditBookmarkModal"]').click(function() {
                $('#editBookmarkHeader').text('Edit Bookmark (Id: ' + $(this).attr('data-bs-editId') + ')');
                $('#editBookmarkForm').prop('action', "{{ route('bookmarks.edit', ':id')}}".replace(':id', $(this).attr('data-bs-editId')));
                $('#editTitle').val($(this).closest('tr').find('td:nth-child(2)').text());
                $('#editURL').val($(this).closest('tr').find('td:nth-child(3)').text());
            });
        });

    </script>

@stop
