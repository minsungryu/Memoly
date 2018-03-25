<div class="container">
    <h1 class="text-center m-5">회원 목록</h1>
    <ul class="nav justify-content-between">
        <button class="btn btn-primary">선택삭제</button>
        <form action="admin.php" method="get" class="form-inline">
            <select class="form-control mr-2" name="option">
                <option>이메일</option>
                <option <?php if ($_GET['option'] == '닉네임') echo 'selected'; ?>>닉네임</option>
            </select>
            <input class="form-control mr-sm-2" type="search" name="search">
            <button class="btn btn-outline-primary my-2 my-sm-0" type="submit">검색</button>
        </form>
    </ul>
    <?php if ($this->item_count === 0): ?>
        <p class="mt-5 text-center">검색결과가 없습니다.
    <?php else: ?>
    <table class="table table-striped table-hover mt-3">
        <thead>
            <tr>
                <th scope="col" class="text-center"><input type="checkbox"></th>
                <th scope="col" class="text-center">이메일</th>
                <th scope="col" class="text-center">닉네임</th>
                <th scope="col" class="text-center">가입일자</th>
                <th scope="col" class="text-center">마지막로그인</th>
                <th scope="col" class="text-center">이메일 인증</th>
                <th scope="col" class="text-center">작성한 메모 수</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $end = min(count($this->user_list), $this->current_page * $this->page_offset);
                for ($i = 0; $i < $end; $i++):
            ?>
            <tr>
                <td class="text-center"><input type="checkbox"></td>
                <td class="text-center"><?= $this->hideEmail($this->user_list[$i]['user_email']) ?></td>
                <td class="text-center"><?= $this->user_list[$i]['user_nickname'] ?></td>
                <td class="text-center"><?= $this->user_list[$i]['signup_date'] ?></td>
                <td class="text-center"><?= $this->user_list[$i]['last_login'] ?></td>
                <td class="text-center"><?= $this->user_list[$i]['is_verified'] ?></td>
                <td class="text-center"><?= $this->user_list[$i]['memo_count'] ?></td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>    
    <?php $this->pageNavigator(); ?>
    <?php endif; ?>
</div>