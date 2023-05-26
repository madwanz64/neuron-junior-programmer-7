@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="card">
                <div class="card-header bg-gradient">
                    User Data
                </div>

                <div class="card-body" style="overflow-x: scroll">
                    <button class="btn btn-secondary bg-gradient mb-3" data-bs-toggle="modal"
                            data-bs-target="#add-user-modal">
                        <i class="bi bi-plus-lg"></i> Add New Users
                    </button>
                    <table id="user-data-table" class="table table-striped table-responsive">
                        <caption>User List</caption>
                        <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Action</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="add-user-modal" tabindex="-1" aria-labelledby="add-user-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="add-user-modal-label">Add New User</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="add-user-form">
            <div class="modal-body">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Your name">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="edit-user-modal" tabindex="-1" aria-labelledby="edit-user-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="edit-user-modal-label">Edit User</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="edit-user-form">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="id" name="id">
                    <div class="mb-3">
                        <label for="name_edit" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name_edit" name="name" placeholder="Your name" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="email_edit" class="form-label">E-mail address</label>
                        <input type="email" class="form-control" id="email_edit" name="email" placeholder="name@example.com" disabled>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('customjs')
    <script>
        $(document).ready(function () {
            let table = $('#user-data-table').DataTable({
                processing : true,
                serverSide : true,
                ajax : {
                    url : '{{ route('users.index') }}'
                },
                columns : [
                    {data : 'name'},
                    {data : 'email'},
                    {
                        class : 'text-center text-nowrap',
                        data : 'id',
                        defaultContent : '',
                        orderable : false,
                        render : function (t) {
                            let editButton = `
                            <button class="btn btn-info bg-gradient" data-bs-toggle="modal"
                            data-bs-target="#edit-user-modal" data-id="${t}">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>`
                            let deleteButton = `
                            <button class="btn btn-danger bg-gradient delete-data-button"  data-id="${t}">
                                <i class="bi bi-trash"></i> Delete
                            </button>`
                            return editButton + deleteButton
                        },
                        width : '200px'
                    },
                ]
            })

            $('#add-user-form').on('submit', function (e) {
                e.preventDefault()

                $.ajax({
                    url : '{{ route('users.store') }}',
                    type : 'POST',
                    data : new FormData(this),
                    processData : false,
                    contentType : false,
                    success : function (response) {
                        $('#add-user-modal').modal('hide');
                        table.draw();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            timer: 3000,
                            timerProgressBar: true,
                        })
                    },
                    error : function (xhr, status, error) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $('#add-user-form :input').each(function () {
                                $(this).parent().children('div.invalid-feedback').remove()
                                $(this).removeClass('is-invalid')
                                if (errors.hasOwnProperty($(this).attr('name'))) {
                                    let errorMessage = document.createElement('div');
                                    errorMessage.classList.add('invalid-feedback');
                                    errorMessage.innerHTML = errors[$(this).attr('name')];
                                    $(this).addClass('is-invalid')
                                    $(this).parent().append(errorMessage);
                                }
                            })
                        }
                        Toast.fire({
                            icon : 'error',
                            title : error
                        });
                    }
                })
            })

            $('#edit-user-modal').on('show.bs.modal', function (event) {
                let button = $(event.relatedTarget);
                let dataId = button.data('id');

                let url = '{{ route('users.show', ['user' => '%%user%%']) }}'
                url = url.replace('%%user%%', dataId)

                $.ajax({
                    url : url,
                    type : 'GET',
                    contentType : 'application/json',
                    success : function (response) {
                        let data = response.data;

                        $('#edit-user-form :input').each(function() {
                            let elementName = $(this).attr('name'); // Get the name attribute of the element
                            // Check if the element name exists in the JSON response
                            if (elementName && data.hasOwnProperty(elementName)) {
                                let elementType = $(this).prop('tagName').toLowerCase(); // Get the type of element
                                $(this).removeAttr('readonly');
                                let type = $(this).prop('type').toLowerCase();
                                if (type === 'file') {
                                    return
                                }
                                if (elementType === 'input' || elementType === 'textarea') {
                                    let elementValue = data[elementName]; // Get the corresponding value from the JSON response
                                    $(this).val(elementValue); // Set the value of the input or textarea field
                                } else if (elementType === 'select') {
                                    let elementValue = data[elementName]; // Get the corresponding value from the JSON response
                                    $(this).val(elementValue); // Set the selected option of the select field
                                }
                            }
                        });
                    }
                })
            }).on('hide.bs.modal', function (event) {
                $('#edit-user-form').trigger('reset');
                $('#edit-user-form :input').each(function () {
                    $(this).parent().children('div.invalid-feedback').remove();
                    $(this).removeClass('is-invalid');
                    $(this).attr('readonly', true)
                });
            })

            $('#edit-user-form').on('submit', function (e) {
                e.preventDefault()
                let form = $('#edit-user-form')
                let formData = form.serializeArray().reduce(function(result, field) {
                    result[field.name] = field.value;
                    return result;
                }, {});

                let id = $(`#edit-user-form [name="id"]`).val();

                let url = '{{ route('users.update', ['user' => '%%user%%']) }}';
                url = url.replace('%%user%%', id);

                $.ajax({
                    url : url,
                    type : 'PUT',
                    data : JSON.stringify(formData),
                    dataType : 'json',
                    contentType : 'application/json',
                    success : function (response) {
                        $('#edit-user-modal').modal('hide')
                        table.draw()
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            timer: 3000,
                            timerProgressBar: true,
                        })
                    },
                    error : function (xhr, status, error) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $('#edit-user-form :input').each(function () {
                                $(this).parent().children('div.invalid-feedback').remove()
                                $(this).removeClass('is-invalid')
                                if (errors.hasOwnProperty($(this).attr('name'))) {
                                    let errorMessage = document.createElement('div');
                                    errorMessage.classList.add('invalid-feedback');
                                    errorMessage.innerHTML = errors[$(this).attr('name')];
                                    $(this).addClass('is-invalid')
                                    $(this).parent().append(errorMessage);
                                }
                            })
                        }
                        Toast.fire({
                            icon : 'error',
                            title : error
                        });
                    }
                })
            })

            $('#user-data-table').on('click', '.delete-data-button', function (event) {
                let button = $(this);
                let dataId = button.data('id')

                Swal.fire({
                    icon : 'warning',
                    title: 'Do you want to delete the data?',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                }).then((result) => {
                    if (result.isConfirmed) {
                        let url = '{{ route('users.destroy', ['user' => '%%user%%']) }}'
                        url = url.replace('%%user%%', dataId)
                        $.ajax({
                            url : url,
                            type : 'DELETE',
                            success : function (response) {
                                table.draw()
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message,
                                    timer: 3000,
                                    timerProgressBar: true,
                                })
                            },
                            error : function (xhr, status, error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON.message,
                                    timer: 3000,
                                    timerProgressBar: true,
                                })
                            }
                        })
                    }
                })
            })
        })
    </script>
@endpush
