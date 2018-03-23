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
    <?php if (empty($this->user_list) || count($this->user_list) == 0 || !isset($this->user_count) || $this->user_count == 0): ?>
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
    <nav>
        <ul class="pagination justify-content-center">
            <li class="page-item <?php if ($this->current_page == 1) echo 'disabled'; ?>"><a class="page-link" href="<?= $this->getPage($this->current_page - 5) ?>" tabindex="-1">Previous</a></li>
            <?php
                $min = max($this->current_page - 2, 1);
                $max = min($this->current_page + 2, $this->getLastPage());
                foreach(range($min, $max) as $page):
            ?>
                <li class="page-item <?php if ($page == $this->current_page) echo 'active'; ?>"><a class="page-link" href="<?= $this->getPage($page) ?>"><?= $page ?></a></li>
            <?php endforeach; ?>
            <li class="page-item <?php if ($this->current_page == $this->getLastPage()) echo 'disabled'; ?>"><a class="page-link" href="<?= $this->getPage($this->current_page + 5) ?>">Next</a></li>
        </ul>
    </nav>
    <?php endif; ?>
</div>