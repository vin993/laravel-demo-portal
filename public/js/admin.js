$(document).ready(function () {
    $('input[name="phone"]').mask('(000) 000 0000');
    // $('#dataTable').DataTable({
    //     order: [[0, 'asc']],
    //     pageLength: 25,
    //     language: {
    //         search: "Search users: ",
    //         lengthMenu: "Show _MENU_ users per page",
    //         info: "Showing _START_ to _END_ of _TOTAL_ users"
    //     },
    //     columnDefs: [
    //         { orderable: false, targets: [5, 6] },
    //         {
    //             targets: 4,
    //             render: function (data, type, row) {
    //                 return type === 'sort' ? $(data).text() : data;
    //             }
    //         }
    //     ],
    //     dom: '<"row"<"col-md-6"l><"col-md-6 text-end"f>>rtip',
    //     responsive: true
    // });
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $('.toggle-approve').on('click', function (e) {
        e.preventDefault();
        let button = $(this);
        let userId = button.data('id');
    
        $.ajax({
            url: `/admin/users/${userId}/toggle-approval`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function (response) {
                if (response.success) {
                    if (response.is_approved) {
                  
                        button.removeClass('btn-success').addClass('btn-warning');
                        button.html('<i class="fas fa-ban me-1"></i>Revoke');
    
                      
                        button.closest('tr').find('.badge[data-status="approval"]')
                            .removeClass('bg-warning')
                            .addClass('bg-success')
                            .text('Approved');
    
                    } else {
                       
                        button.removeClass('btn-warning').addClass('btn-success');
                        button.html('<i class="fas fa-check me-1"></i>Approve');
    
                  
                        button.closest('tr').find('.badge[data-status="approval"]')
                            .removeClass('bg-success')
                            .addClass('bg-warning')
                            .text('Pending');
                    }
                    toastr.success(response.message);
                }
            },
            error: function () {
                toastr.error('An error occurred. Please try again.');
            }
        });
    });


    let selectedUserId;

    $('.delete-user').click(function () {
        selectedUserId = $(this).data('id');
        $('#deleteUserForm').attr('action', `/admin/users/${selectedUserId}`);
        $('#deleteUserModal').modal('show');
    });

    $('#deleteUserForm').submit(function (e) {
        e.preventDefault();

        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.html();

        $submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Deleting...').prop('disabled', true);

        $.ajax({
            url: $(this).attr('action'),
            type: 'DELETE',
            data: $(this).serialize(),
            success: function (response) {
                if (response.success) {
                    $('#deleteUserModal').modal('hide');

                    toastr.success('User deleted successfully', '', {
                        timeOut: 1000,
                        closeButton: false,
                        onHidden: function () {
                            window.location.reload();
                        }
                    });
                } else {
                    toastr.error('Failed to delete user');
                    $submitBtn.html(originalText).prop('disabled', false);
                }
            },
            error: function (xhr) {
                toastr.error(xhr.responseJSON?.message || 'An error occurred while deleting the user');
                $submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });



    $('.userModal').on('shown.bs.modal', function () {
        $(this).find('.select2-multiple').each(function () {
            let $select = $(this);
            let $modal = $select.closest('.modal');
            let isPrimary = $modal.find('input[name="is_primary"]').is(':checked');
            let invitedBy = $modal.data('invited-by');

            let $container = $('<div>').addClass('select2-container-custom').appendTo($modal.find('.modal-content'));

            if (invitedBy) {
                $select.prop('disabled', true);
                $select.closest('.form-group').find('label').append(' <small class="text-muted">(Inherited from primary user)</small>');
            }
            $select.select2({
                dropdownParent: $container,
                width: '100%',
                tags: false,
                placeholder: $select.data('placeholder') || "Select option",
                allowClear: true,
                closeOnSelect: true,
                ajax: {
                    url: function () {
                        if ($select.attr('name').includes('industry')) {
                            return '/api/industries/search';
                        } else if ($select.attr('name').includes('manufacturer')) {
                            return '/api/manufacturers/search';
                        } else {
                            return '/api/dealers/search';
                        }
                    },
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term || '',
                            selected: $(this).val()
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function (item) {
                                return {
                                    id: item.id,
                                    text: item.name
                                };
                            })
                        };
                    },
                    cache: true
                }
            });

            
        });
    });


    $('.select2-multiple').each(function() {
        let $select = $(this);
        if ($select.attr('name') === 'additional_dealer_id[]') {
            $select.select2({
                tags: false,
                placeholder: "Search and select additional dealers",
                allowClear: true,
                ajax: {
                    url: '/api/dealers/search',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term || '',
                            exclude_company: $select.closest('.form-group')
                                                  .find('input[disabled]').val()
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function (item) {
                                return {
                                    id: item.id,
                                    text: item.name
                                };
                            })
                        };
                    },
                    cache: true
                }
            });
        }
    });



    $('.save-user-details').on('click', function () {
        const userId = $(this).data('user-id');
        const $form = $(`#editUserForm${userId}`);
        const $button = $(this);
        const originalText = $button.html();

        const isPrimary = $form.find(`#isPrimary${userId}`).is(':checked');

        const formData = new FormData($form[0]);
        formData.set('is_primary', isPrimary ? '1' : '0');

        $button.html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...').prop('disabled', true);

        $.ajax({
            url: `/admin/users/${userId}/update`,
            type: 'POST',
            data: new URLSearchParams(formData).toString(),
            success: function (response) {
                if (response.success) {
                    toastr.success('User updated successfully');
                    window.location.hash = `user-${userId}`;
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    toastr.error('Failed to update user interests');
                    $button.html(originalText).prop('disabled', false);
                }
            },
            error: function (xhr) {
                toastr.error(xhr.responseJSON?.message || 'An error occurred while updating user interests');
                $button.html(originalText).prop('disabled', false);
            }
        });
    });

    $('#industry_interests').select2({
        tags: false,
        placeholder: "Search and select existing industries",
        multiple: true,
        ajax: {
            url: '/api/industries/search',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term || '',
                    selected: $(this).val()
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    })
                };
            },
            cache: true
        },
        createTag: function () {
            return null;
        }
    });

    // Dealer Select2
    $('#dealer_id').select2({
        tags: false,
        placeholder: "Search and select dealer companies",
        multiple: true,
        ajax: {
            url: '/api/dealers/search',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term || ''
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    })
                };
            },
            cache: true
        },
        createTag: function () {
            return null;
        }
    });


    $('#manufacturer').select2({
        tags: false,
        placeholder: "Search and select manufacturers",
        multiple: true,
        closeOnSelect: true,
        dropdownParent: $(this).closest('.modal-body'),
        minimumInputLength: 0,
        minimumResultsForSearch: 0,
        allowClear: true,
        ajax: {
            url: '/api/manufacturers/search',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term || '',
                    selected: $(this).val()
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    })
                };
            },
            cache: true
        },
        createTag: function () {
            return null;
        }
    });

    if (window.location.hash) {
        const hash = window.location.hash.substring(1);
        if (hash.startsWith('user-')) {
            const userId = hash.replace('user-', '');
            setTimeout(() => {
                $(`#userModal${userId}`).modal('show');
                history.pushState('', document.title, window.location.pathname);
            }, 500);
        }
    }



    // Country Select2
    $('#country').select2({
        placeholder: "Select Country",
        allowClear: true,
        ajax: {
            url: '/api/countries/search',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term || '',
                    page: params.page || 1
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return {
                            id: item.id,
                            text: item.name,
                            code: item.code
                        };
                    })
                };
            },
            cache: true
        }
    });


    // State Select2
    $('#state').select2({
        placeholder: "Select State",
        allowClear: true,
        ajax: {
            url: '/api/states/search',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term || '',
                    country_id: $('#country').val(),
                    page: params.page || 1
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    });

    // City Select2
    $('#city').select2({
        placeholder: "Select City",
        allowClear: true,
        ajax: {
            url: '/api/cities/search',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term || '',
                    state_id: $('#state').val(),
                    page: params.page || 1
                };
            },
            processResults: function (data) {
                return {
                    results: Object.entries(data).map(([id, name]) => ({
                        id: id,
                        text: name
                    }))
                };
            },
            cache: true
        }
    }).on('select2:select', function (e) {
        const countryData = $('#country').select2('data')[0];
        const stateData = $('#state').select2('data')[0];
        const cityName = e.params.data.text;

        if (!countryData || !stateData || !cityName) {
            console.error('Missing required location data');
            return;
        }

        const countryCode = countryData.code.toLowerCase();
        const stateCode = stateData.state_code.toLowerCase();
        const formattedCity = cityName.toLowerCase().replace(/\s+/g, '%20');

        $('#postal_code').attr('disabled', true);

        $.ajax({
            url: `https://api.zippopotam.us/${countryCode}/${stateCode}/${formattedCity}`,
            method: 'GET',
            timeout: 5000,
            success: function (response) {
                if (response && response.places && response.places[0]) {
                    const postalCode = response.places[0]['post code'];
                    $('#postal_code').val(postalCode);
                }
            },
            error: function (xhr, status, error) {
                // console.error('Error fetching postal code:', error);
                $('#postal_code').val('');
            },
            complete: function () {
                $('#postal_code').attr('disabled', false);
            }
        });
    });

    // Handle dependencies
    $('#country').on('change', function () {
        $('#state').val(null).trigger('change');
        $('#city').val(null).trigger('change');
    });

    $('#state').on('change', function () {
        $('#city').val(null).trigger('change');
    });


    // Using PDF.js library
    function displayPDF(url) {
        return `
        <div class="pdf-container" style="width: 100%; height: 600px;">
            <object data="${url}" type="application/pdf" width="100%" height="100%">
                <embed src="${url}" type="application/pdf" width="100%" height="100%">
                    <p>This browser does not support PDFs. Please download the PDF to view it: 
                        <a href="${url}" download>Download PDF</a>
                    </p>
                </embed>
            </object>
        </div>
    `;
    }


    // Using PapaParse library
    function displayCSV(url) {
        return new Promise((resolve, reject) => {
            Papa.parse(url, {
                download: true,
                complete: function (results) {
                    let html = '<div class="table-responsive"><table class="table table-bordered">';
                    results.data.forEach((row, index) => {
                        html += '<tr>';
                        if (index === 0) {
                            row.forEach(cell => html += `<th>${cell}</th>`);
                        } else {
                            row.forEach(cell => html += `<td>${cell}</td>`);
                        }
                        html += '</tr>';
                    });
                    html += '</table></div>';
                    resolve(html);
                },
                error: function (error) {
                    reject(error);
                }
            });
        });
    }

    // Using SheetJS library
    async function displayExcel(url) {
        try {
            const response = await fetch(url);
            const arrayBuffer = await response.arrayBuffer();
            const data = new Uint8Array(arrayBuffer);
            const workbook = XLSX.read(data, { type: 'array' });

            let html = '<div class="excel-preview">';
            workbook.SheetNames.forEach(sheetName => {
                const worksheet = workbook.Sheets[sheetName];
                const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

                html += `<h4>${sheetName}</h4>`;
                html += '<div class="table-responsive"><table class="table table-bordered">';
                jsonData.forEach((row, index) => {
                    html += '<tr>';
                    if (index === 0) {
                        row.forEach(cell => html += `<th>${cell}</th>`);
                    } else {
                        row.forEach(cell => html += `<td>${cell}</td>`);
                    }
                    html += '</tr>';
                });
                html += '</table></div>';
            });
            html += '</div>';
            return html;
        } catch (error) {
            console.error('Error displaying Excel file:', error);
            return `<div class="alert alert-danger">Error displaying Excel file</div>`;
        }
    }

    // Using Mammoth.js library
    async function displayWord(url) {
        try {
            const response = await fetch(url);
            const arrayBuffer = await response.arrayBuffer();
            const result = await mammoth.convertToHtml({ arrayBuffer: arrayBuffer });
            return `
            <div class="word-preview p-4 bg-white">
                ${result.value}
            </div>
        `;
        } catch (error) {
            console.error('Error displaying Word document:', error);
            return `<div class="alert alert-danger">Error displaying Word document</div>`;
        }
    }

});





