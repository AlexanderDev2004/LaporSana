<form action="{{ route('admin.users.update', $user) }}" method="POST" id="form-edit" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div id="modal-master" class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" id="username" class="form-control" 
                                   value="{{ $user->username }}" required>
                            <small id="error-username" class="error-text form-text text-danger"></small>
                        </div>
                        
                        <div class="form-group">
                            <label>Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" 
                                   value="{{ $user->name }}" required>
                            <small id="error-name" class="error-text form-text text-danger"></small>
                        </div>
                        
                        <div class="form-group">
                            <label>Password (Kosongkan jika tidak ingin mengubah)</label>
                            <input type="password" name="password" id="password" class="form-control">
                            <small id="error-password" class="error-text form-text text-danger"></small>
                        </div>
                        
                        <div class="form-group">
                            <label>Role <span class="text-danger">*</span></label>
                            <select name="roles_id" id="roles_id" class="form-control" required>
                                <option value="">- Pilih Role -</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->roles_id }}" {{ $user->roles_id == $role->roles_id ? 'selected' : '' }}>
                                        {{ $role->roles_nama }}
                                    </option>
                                @endforeach
                            </select>
                            <small id="error-roles_id" class="error-text form-text text-danger"></small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <!-- Field NIM -->
                        <div class="form-group nim-field">
                            <label>Nomor Induk Mahasiswa (NIM)</label>
                            <input type="text" name="NIM" id="NIM" class="form-control" value="{{ $user->NIM }}">
                            <small class="form-text text-muted">Isi kolom NIM jika user adalah Mahasiswa</small>
                            <small id="error-NIM" class="error-text form-text text-danger"></small>
                        </div>
                        
                        <!-- Field NIP -->
                        <div class="form-group nip-field">
                            <label>Nomor Induk Pegawai (NIP)</label>
                            <input type="text" name="NIP" id="NIP" class="form-control" value="{{ $user->NIP }}">
                            <small class="form-text text-muted">Isi kolom NIP jika user selain Mahasiswa</small>
                            <small id="error-NIP" class="error-text form-text text-danger"></small>
                        </div>
                        
                        <div class="form-group">
                            <label>Avatar</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="avatar" id="avatar" class="custom-file-input">
                                    <label class="custom-file-label" for="avatar">Pilih file</label>
                                </div>
                            </div>
                            <small class="form-text text-muted">Format: jpeg, png, jpg, gif (Maks: 2MB)</small>
                            <small id="error-avatar" class="error-text form-text text-danger"></small>
                        </div>
                        
                        <div class="form-group preview-img" style="{{ $user->avatar ? 'display: block;' : 'display: none;' }}">
                            <label>Preview</label>
                            <div>
                                <img id="preview" src="{{ $user->avatar ? asset('storage/'.$user->avatar) : '' }}" 
                                    alt="Preview" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    // Preview image before upload
    $('#avatar').change(function() {
        const file = this.files[0];
        
        if (file) {
            // Create FileReader and handle preview
            let reader = new FileReader();
            reader.onload = function(event) {
                $('.preview-img').show();
                $('#preview').attr('src', event.target.result);
            }
            reader.readAsDataURL(file);
            
            // Set file name in label
            $(this).next('.custom-file-label').html(file.name);
            $('.custom-file-label').text(file.name);
            
            // BS4 specific handling for custom-file-input
            if (typeof bsCustomFileInput !== 'undefined') {
                bsCustomFileInput.init();
            }
        }
    });
    
    // Add BS4 custom file input initialization if available
    if (typeof bsCustomFileInput !== 'undefined') {
        bsCustomFileInput.init();
    } else {
        // Fallback for custom file input if BS4 plugin not available
        $(document).on('change', '.custom-file-input', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName || 'Pilih file');
        });
    }
    
    // Highlight appropriate field based on role selection
    $('#roles_id').change(function() {
        const roleName = $('#roles_id option:selected').text().trim().toLowerCase();
        
        // Reset highlighting
        $('.nim-field, .nip-field').removeClass('bg-light');
        $('#NIM, #NIP').prop('required', false);
        
        // Highlight appropriate field based on selected role
        if (roleName.includes('mahasiswa')) {
            $('.nim-field').addClass('bg-light');
            $('#NIM').prop('required', true);
        } else if (roleName && roleName !== '' && roleName !== '- pilih role -') {
            $('.nip-field').addClass('bg-light');
            $('#NIP').prop('required', true);
        }
    });
    
    // Trigger change event on page load if a role is already selected
    if ($('#roles_id').val()) {
        $('#roles_id').trigger('change');
    }

    // Form validation and AJAX submission
    $("#form-edit").validate({
        rules: {
            username: {
                required: true,
                minlength: 3,
                maxlength: 50
            },
            name: {
                required: true,
                minlength: 3,
                maxlength: 100
            },
            password: {
                minlength: 6
            },
            roles_id: {
                required: true
            },
            NIM: {
                maxlength: 20
            },
            NIP: {
                maxlength: 20
            },
            avatar: {
                extension: "jpg|jpeg|png|gif",
                filesize: 2097152 // 2MB
            }
        },
        messages: {
            avatar: {
                extension: "Format file harus jpg, jpeg, png, atau gif",
                filesize: "Ukuran file maksimal 2MB"
            }
        },
        submitHandler: function(form) {
            // Create FormData object to handle file uploads
            var formData = new FormData(form);
            
            $.ajax({
                url: form.action,
                type: form.method,
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('.error-text').text('');
                    $('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
                },
                success: function(response) {
                    if (response.status) {
                        $('#myModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                        dataUsers.ajax.reload();
                    } else {
                        $('.error-text').text('');
                        $.each(response.msgField, function(prefix, val) {
                            $('#error-'+prefix).text(val[0]);
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan pada server'
                    });
                },
                complete: function() {
                    $('button[type="submit"]').prop('disabled', false).html('Update');
                }
            });
            return false;
        },
        errorElement: 'span',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function(element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });
    
    // Tambahkan validator untuk ukuran file
    $.validator.addMethod('filesize', function(value, element, param) {
        return this.optional(element) || (element.files[0] && element.files[0].size <= param);
    });
});
</script>