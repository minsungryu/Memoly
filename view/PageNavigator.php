<nav>
    <ul class="pagination justify-content-center">
        <li class="page-item <?php if ($this->current_page == 1) echo 'disabled'; ?>"><a class="page-link" href="<?= $this->getPage($this->current_page - 5) ?>" tabindex="-1">이전</a></li>
        <?php
            $min = max($this->current_page - 2, 1);
            $max = min($this->current_page + 2, $this->getLastPage());
            foreach(range($min, $max) as $page):
        ?>
            <li class="page-item <?php if ($page == $this->current_page) echo 'active'; ?>"><a class="page-link" href="<?= $this->getPage($page) ?>"><?= $page ?></a></li>
        <?php endforeach; ?>
        <li class="page-item <?php if ($this->current_page == $this->getLastPage()) echo 'disabled'; ?>"><a class="page-link" href="<?= $this->getPage($this->current_page + 5) ?>">다음</a></li>
    </ul>
</nav>