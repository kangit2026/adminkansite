<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <button type="button" class="nav-link" :class="lang === 'uk' && 'active'" @click="lang = 'uk'">UA</button>
    </li>
    <li class="nav-item">
        <button type="button" class="nav-link" :class="lang === 'en' && 'active'" @click="lang = 'en'">EN</button>
    </li>
</ul>
