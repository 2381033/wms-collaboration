@component('mail::message')
    <?php
    $date_from = date('d', strtotime('-7 days'));
    $date_to = date('d', strtotime('-1 days'));
    $month_from = date('M', strtotime('-7 days'));
    $month_to = date('M', strtotime('-1 days'));
    $year_from = date('Y', strtotime('-7 days'));
    $year_to = date('Y', strtotime('-1 days'));
    $string_date = '';
    if ($year_from == $year_to) {
        if ($month_from == $month_to) {
            $string_date = $date_from . ' - ' . $date_to . ',' . $month_to . '/' . $year_to;
        } else {
            $string_date = $date_from . ' ' . $month_from . ' - ' . $date_to . ' ' . $month_to . '/' . $year_to;
        }
    } else {
        $string_date = $date_from . ' ' . $month_from . ' ' . $year_from . ' - ' . $date_to . ' ' . $month_to . ' ' . $year_to;
    }
    ?>

    Berikut ini kami sampaikan data Inventory - Cycle Count<br> untuk periode <?= $string_date ?>.<br>

    <br>
    Thanks,<br>
    PT Masaji Kargosentra Tama
@endcomponent
