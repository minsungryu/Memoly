/**
 * 전체 회원 선택/해제
 */
$("#check-all").click(function(){
    $('input:checkbox').not(this).prop('checked', this.checked);
});

$('#delete-button').click(function() {
    var user_emails = $('#user-table tbody').find('input[type="checkbox"]:checked').map(function(index, element) {
        return { name: 'user_emails[]', value: $(element).data('email') };
    });

    $.ajax({
        url: './admin.php',
        type: 'post',
        data: $.param(user_emails),
        success: function (result) {
            if (result != '0') {
                alert('선택한 회원이 삭제되었습니다!');
                window.location.reload(true);
            } else {
                alert('삭제 요청을 실패했습니다.');
            }
        },
        error: function (err) {
            alert(err.responseJSON);
        }
    })
});

function getSelected() {
    var selectedIds = $('.table').columns().checkboxes.selected()[0];
    console.log(selectedIds)
 
    selectedIds.forEach(function(selectedId) {
        alert(selectedId);
    });
 }