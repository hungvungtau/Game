```php
<?php

include('class/UserLink.php');

$user = new User1();

/*
 * Lấy toàn bộ users
 */
$contents = $user->getdata1('users', true);

?>

<div class="container-fluid mt-4">

    <h2>Danh sách Users</h2>

    <div id="message"
         class="alert"
         style="display:none;">
    </div>


    <div class="table-responsive">

        <table class="table table-bordered table-hover"
               id="userTable">

            <thead class="thead-dark">

                <tr>

                    <th width="40px">
                        <input type="checkbox" id="checkAll">
                    </th>

                    <th width="100px">
                        Actions
                    </th>

                    <th>ID</th>

                    <th>Id_Pm</th>

                    <th>Id_pmw1</th>

                    <th>Họ tên</th>

                    <th>Email</th>

                    <th>Phone</th>

                    <th>Password</th>

                    <th>Gender</th>

                    <th>Designation</th>

                    <th>Image</th>

                    <th>Date</th>

                    <th>Solancap</th>

                    <th>Solandung</th>

                    <th>Status</th>

                    <th>Serial Computer</th>

                    <th>Xoa</th>

                </tr>

            </thead>


            <tbody>

            <?php if(empty($contents)): ?>

                <tr>

                    <td colspan="18"
                        class="text-center">

                        Chưa có users

                    </td>

                </tr>

            <?php else: ?>


                <?php foreach($contents as $ct): ?>

                    <tr data-id="<?php echo (int)$ct['id']; ?>">


                        <!-- CHECKBOX -->

                        <td>

                            <input type="checkbox"
                                   class="user-checkbox"
                                   value="<?php echo (int)$ct['id']; ?>">

                        </td>


                        <!-- ACTION -->

                        <td>

                            <div class="normal-buttons">

                                <button
                                    class="btn btn-sm btn-info edit-btn">

                                    <i class="fas fa-edit"></i>

                                </button>

                            </div>


                            <div class="edit-buttons"
                                 style="display:none;">

                                <button
                                    class="btn btn-sm btn-success save-inline-btn">

                                    <i class="fas fa-save"></i>

                                </button>


                                <button
                                    class="btn btn-sm btn-secondary cancel-inline-btn">

                                    <i class="fas fa-times"></i>

                                </button>

                            </div>

                        </td>


                        <!-- ID -->

                        <td>

                            <?php echo (int)$ct['id']; ?>

                        </td>


                        <!-- Id_Pm -->

                        <td class="editable"
                            data-field="Id_Pm">

                            <?php
                            echo htmlspecialchars(
                                $ct['Id_Pm'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <!-- Id_pmw1 -->

                        <td class="editable"
                            data-field="Id_pmw1">

                            <?php
                            echo htmlspecialchars(
                                $ct['Id_pmw1'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <!-- HOTEN -->

                        <td class="editable"
                            data-field="Hoten">

                            <?php
                            echo htmlspecialchars(
                                $ct['Hoten'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <!-- EMAIL -->

                        <td class="editable"
                            data-field="Email">

                            <?php
                            echo htmlspecialchars(
                                $ct['Email'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <!-- PHONE -->

                        <td class="editable"
                            data-field="Phone">

                            <?php
                            echo htmlspecialchars(
                                $ct['Phone'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <!-- PASSWORD -->

                        <td class="editable password-field"
                            data-field="Password">

                            <span class="text-muted">
                                ********
                            </span>

                        </td>


                        <!-- GENDER -->

                        <td class="editable"
                            data-field="gender">

                            <?php
                            echo htmlspecialchars(
                                $ct['gender'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <!-- DESIGNATION -->

                        <td class="editable"
                            data-field="designation">

                            <?php
                            echo htmlspecialchars(
                                $ct['designation'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <!-- IMAGE -->

                        <td class="editable"
                            data-field="image">

                            <?php
                            echo htmlspecialchars(
                                $ct['image'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <!-- DATE -->

                        <td class="editable"
                            data-field="Date">

                            <?php
                            echo htmlspecialchars(
                                $ct['Date'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <!-- SOLANCAP -->

                        <td class="editable"
                            data-field="Solancap">

                            <?php
                            echo htmlspecialchars(
                                $ct['Solancap'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <!-- SOLANDUNG -->

                        <td class="editable"
                            data-field="Solandung">

                            <?php
                            echo htmlspecialchars(
                                $ct['Solandung'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <!-- STATUS -->

                        <td class="editable"
                            data-field="status">

                            <?php
                            echo htmlspecialchars(
                                $ct['status'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                        <!-- SERIAL COMPUTER -->
                        <!-- KHÔNG CHO SỬA -->

                        <td>

                            <span class="text-danger">

                                <?php
                                echo htmlspecialchars(
                                    $ct['Serial_computer'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </span>

                            <br>

                            <small class="text-muted">
                                Không được sửa
                            </small>

                        </td>


                        <!-- XOA -->

                        <td class="editable"
                            data-field="Xoa">

                            <?php
                            echo htmlspecialchars(
                                $ct['Xoa'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>

                        </td>


                    </tr>

                <?php endforeach; ?>


            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>


<script>

$(document).ready(function() {


    /*
     * ==========================
     * CHECK ALL
     * ==========================
     */

    $('#checkAll').click(function() {

        $('.user-checkbox')
            .prop('checked', this.checked);

    });


    /*
     * ==========================
     * EDIT
     * ==========================
     */

    $(document).on(
        'click',
        '.edit-btn',
        function()
    {

        const row = $(this).closest('tr');


        row.find('.editable').each(function() {

            const cell = $(this);

            const field =
                cell.data('field');


            /*
             * Password
             *
             * Không lấy password hiện tại.
             * Để trống nghĩa là giữ nguyên.
             */

            if(field === 'Password') {

                cell.data(
                    'old',
                    ''
                );

                cell.html(
                    '<input type="password" ' +
                    'class="form-control form-control-sm" ' +
                    'placeholder="Để trống nếu không đổi">'
                );

            }

            else {

                const val =
                    cell.text().trim();

                cell.data(
                    'old',
                    val
                );

                cell.html(
                    $('<input>', {

                        type: 'text',

                        class:
                        'form-control form-control-sm',

                        value: val

                    })
                );

            }

        });


        row.find('.normal-buttons')
           .hide();


        row.find('.edit-buttons')
           .show();

    });


    /*
     * ==========================
     * CANCEL
     * ==========================
     */

    $(document).on(
        'click',
        '.cancel-inline-btn',
        function()
    {

        const row =
            $(this).closest('tr');


        row.find('.editable').each(
            function()
        {

            const cell = $(this);

            const field =
                cell.data('field');


            if(field === 'Password') {

                cell.html(
                    '<span class="text-muted">' +
                    '********' +
                    '</span>'
                );

            }

            else {

                cell.text(
                    cell.data('old')
                );

            }

        });


        row.find('.edit-buttons')
           .hide();


        row.find('.normal-buttons')
           .show();

    });


    /*
     * ==========================
     * SAVE
     * ==========================
     */

    $(document).on(
        'click',
        '.save-inline-btn',
        function()
    {

        const row =
            $(this).closest('tr');


        const id =
            row.data('id');


        let data = {

            id: id

        };


        /*
         * Lấy dữ liệu các field
         */

        row.find('.editable').each(
            function()
        {

            const cell = $(this);

            const field =
                cell.data('field');


            const input =
                cell.find('input');


            const value =
                input.val();


            data[field] =
                value;

        });


        /*
         * Thông báo
         */

        $('#message')
            .removeClass()
            .addClass(
                'alert alert-info'
            )
            .text(
                'Đang lưu...'
            )
            .show();


        /*
         * AJAX
         */

        $.ajax({

            url:
            'update_users.php',

            type:
            'POST',

            data:
            data,

            dataType:
            'json',


            success:
            function(response)
            {

                if(response.success) {


                    /*
                     * Cập nhật giao diện
                     */

                    row.find('.editable').each(
                        function()
                    {

                        const cell =
                            $(this);


                        const field =
                            cell.data(
                                'field'
                            );


                        const value =
                            data[field];


                        /*
                         * Password
                         */

                        if(
                            field ===
                            'Password'
                        ) {

                            cell.html(
                                '<span class="text-muted">' +
                                '********' +
                                '</span>'
                            );

                        }

                        else {

                            cell.text(
                                value
                            );

                        }

                    });


                    row.find('.edit-buttons')
                       .hide();


                    row.find('.normal-buttons')
                       .show();


                    $('#message')
                        .removeClass()
                        .addClass(
                            'alert alert-success'
                        )
                        .text(
                            'Cập nhật users thành công!'
                        )
                        .show()
                        .delay(2000)
                        .fadeOut();

                }

                else {

                    $('#message')
                        .removeClass()
                        .addClass(
                            'alert alert-danger'
                        )
                        .text(
                            'Lỗi: ' +
                            response.message
                        )
                        .show();

                }

            },


            error:
            function(xhr)
            {

                console.log(
                    xhr.responseText
                );


                $('#message')
                    .removeClass()
                    .addClass(
                        'alert alert-danger'
                    )
                    .text(
                        'Không thể cập nhật users.'
                    )
                    .show();

            }

        });

    });


});

</script>
```
