function changeSaveButton() {
    $('.memo-ok').text('저장');
    $('.memo-ok').removeClass('btn-primary').addClass('btn-success');
    $('.modal-title').prop('readonly', false);
    $('.modal-body textarea').prop('readonly', false);

    $('.memo-cancel').text('취소');
}

function changeWriteButton() {
    $('.memo-ok').text('수정');
    $('.memo-ok').removeClass('btn-success').addClass('btn-primary');
    $('.modal-title').prop('readonly', true);
    $('.modal-body textarea').prop('readonly', true);

    $('.memo-cancel').text('삭제');
}

$('#memo-modal').on('show.bs.modal', function (e) {
    var modal = $(e.relatedTarget);
    var memo_user = modal.data('memo-user');

    $('.modal-body textarea').bind('input propertychange', function() {
        $('.content-length').text(this.value.length);
    });

    if (modal.attr('id') === 'add-memo') {
        changeSaveButton();
        $('.modal-title').val('제목없음');
        $('.modal-body textarea').val('');
        $('.memo-ok').on('click', function() {    
            if (confirm('저장하시겠습니까?')) {
                var add_memo_title = encodeURIComponent($('.modal-title').val());
                var add_memo_content = encodeURIComponent($('.modal-body textarea').val());

                if (add_memo_title.length > 255) {
                    alert('제목은 255자를 초과할 수 없습니다.');
                    var title_area = $('.modal-title').val();
                    $('.modal-title').val(title_area.substr(0,255));
                    return;
                }

                if (add_memo_content.length > 1000) {
                    alert('메모는 1000자를 초과할 수 없습니다.');
                    var text_area = $('.modal-body textarea').val();
                    $('.modal-body textarea').val(text_area.substr(0,1000));
                    $('.modal-body textarea').trigger('input');
                    return;
                }

                $.ajax({
                    url: './memo.php',
                    type: 'post',
                    data: 'action=ADD&memo_user=' + memo_user + '&memo_title=' + add_memo_title + '&memo_content=' + add_memo_content,
                    success: function(post) {
                        if (post) {
                            alert('저장되었습니다!');
                            window.location.reload(true); // without cache
                        } else {
                            alert('저장에 실패했습니다.');
                        }
                    },
                    error: function(err) {
                        alert(err.responseJSON);
                    }
                });
            }
        });

        $('.memo-cancel').on('click', function() {
            var title = $('.modal-title').val();
            var content = $('.modal-body textarea').val();
            if ((title || content) && !confirm('작성중인 메모가 사라집니다')) {
                return;
            }
            $('#memo-modal').modal('hide');
        });
    } else {
        var memo_id = modal.data('memo-id');
        var memo_title = modal.data('memo-title').toString();
        var memo_content = modal.data('memo-content').toString();
        // var memo_created = modal.data('memo-created');
        // var memo_modified = modal.data('memo-modified');
    
        $('.modal-title').val(memo_title);
        $('.modal-body textarea').val(memo_content);
        $('.content-length').text(memo_content.length);
    
        $('.memo-ok').on('click', function() {    
            var mode = $(this).text();
            if (mode === '수정') {
                changeSaveButton();
            } else if (mode === '저장') {
                if (confirm('수정하시겠습니까?')) {
                    var modified_memo_title = encodeURIComponent($('.modal-title').val());
                    var modified_memo_content = encodeURIComponent($('.modal-body textarea').val());

                    if (modified_memo_title.length > 255) {
                        alert('제목은 255자를 초과할 수 없습니다.');
                        var title_area = $('.modal-title').val();
                        $('.modal-title').val(title_area.substr(0,255));
                        return;
                    }
    
                    if (modified_memo_content.length > 1000) {
                        alert('메모는 1000자를 초과할 수 없습니다.');
                        var text_area = $('.modal-body textarea').val();
                        $('.modal-body textarea').val(text_area.substr(0,1000));
                        $('.modal-body textarea').trigger('input');
                        return;
                    }
                
                    $.ajax({
                        url: './memo.php',
                        // type: 'put',
                        type: 'post',
                        data: 'action=PUT&memo_id='+ memo_id +'&memo_user=' + memo_user + '&memo_title=' + modified_memo_title + '&memo_content=' + modified_memo_content,
                        success: function(put) {
                            if (put) {
                                alert('수정되었습니다!');
                                window.location.reload(true); // without cache
                            } else {
                                alert('수정에 실패했습니다.');
                            }
                            changeWriteButton();
                        },
                        error: function(err) {
                            alert(err.responseJSON);
                            changeWriteButton();
                        }
                    });
                }
                changeWriteButton();
            }
        });
    
        $('.memo-cancel').on('click', function() {
            var mode = $(this).text();
            if (mode === '취소') {
                if (($('.modal-title').val() !== memo_title || $('.modal-body textarea').val() !== memo_content) && confirm('변경된 내용이 사라집니다. 계속하시겠습니까?')) {
                    $('.modal-title').val(memo_title);
                    $('.modal-body textarea').val(memo_content);
                    $('.modal-body textarea').trigger('input');
                }
                changeWriteButton();
            } else if (mode === '삭제') {
                if (confirm('정말로 삭제하시겠습니까?')) {
                    $.ajax({
                        url: './memo.php',
                        type: 'post',
                        // type: 'delete',
                        data: 'action=DELETE&memo_id=' + memo_id +'&memo_user=' + memo_user,
                        success: function(del) {
                            if (del) {
                                alert('삭제되었습니다!');
                                window.location.reload(true); // without cache
                            } else {
                                alert('삭제에 실패했습니다.');
                            }
                        },
                        error: function(err) {
                            alert(err.responseJSON);
                        }
                    });
                }
            }
        });
    }
});

$('#memo-modal').on('hide.bs.modal', function (e) {
    $('.modal-title').val('');
    $('.modal-body textarea').val('');
    $('.content-length').text('');

    $('.modal-body textarea').unbind('input propertychange');

    $('.memo-ok').off('click');
    $('.memo-ok').text('수정');
    $('.memo-ok').removeClass('btn-success').addClass('btn-primary');
    $('.modal-title').prop('readonly', true);
    $('.modal-body textarea').prop('readonly', true);

    $('.memo-cancel').off('click');

    changeWriteButton();
});