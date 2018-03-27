<div class="container">
    <h1 class="text-center m-5">메모 목록</h1>
    <ul class="nav justify-content-between">
      <button id="add-memo" class="btn btn-primary"
        data-toggle="modal"
        data-memo-user="<?= $_SESSION['user_email'] ?>"
        data-target="#memo-modal">추가하기</button>
      <form action="memo.php" method="get" class="form-inline">
          <select class="form-control mr-2" name="option">
              <option>내용</option>
              <option <?php if ($_GET['option'] == '제목') echo 'selected'; ?>>제목</option>
          </select>
          <input class="form-control mr-sm-2" type="search" name="search">
          <button class="btn btn-outline-primary my-2 my-sm-0" type="submit">검색</button>
      </form>
    </ul>
    <?php if ($this->item_count == 0): ?>
      <p class="mt-5 text-center">검색결과가 없습니다.
    <?php else: ?>
    <div class="memo-grid mt-3">
      <?php
        $end = min(count($this->memo_list), $this->current_page * $this->page_offset);
        for ($i = 0; $i < $end; $i++):
      ?>
      <div class="card border-secondary mb-3"
        data-toggle="modal"
        data-memo-id="<?= $this->memo_list[$i]['memo_id'] ?>"
        data-memo-user="<?= $_SESSION['user_email'] ?>"
        data-memo-title="<?= $this->memo_list[$i]['memo_title'] ?>"
        data-memo-content="<?= $this->memo_list[$i]['memo_content'] ?>"
        data-memo-created="<?= $this->memo_list[$i]['memo_created'] ?>"
        data-memo-modified="<?= $this->memo_list[$i]['memo_modified'] ?>"
        data-target="#memo-modal">
        <div class="card-header">
          <?= $this->memo_list[$i]['memo_title'] ?>
        </div>
        <div class="card-body text-secondary">
          <p class="card-text"><?= $this->memo_list[$i]['memo_content'] ?></p>
        </div>
      </div>
      <?php endfor; ?>
    </div>
    <?php $this->pageNavigator(); ?>
    <?php endif; ?>
</div>
<!-- Modal -->
<div class="modal fade" id="memo-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <input class="modal-title form-control-plaintext form-control-lg" type="text" id="memo-modal-title" readonly></input>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <textarea readonly></textarea>
      </div>
      <div class="modal-footer">
        <div>글자수 <span class="content-length">0</span>/1000</div>
        <div>
          <button type="button" class="memo-ok btn btn-primary">수정</button>
          <button type="button" class="memo-cancel btn btn-dark">삭제</button>
        </div>
      </div>
    </div>
  </div>
</div>