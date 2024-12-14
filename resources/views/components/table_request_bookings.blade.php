<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th data-type="numeric">รายการที่<span class="resize-handle"></span></th>
                <th data-type="text-short">ชื่อกิจกรรม<span class="resize-handle"></span></th>
                <th data-type="text-short">วันที่จองเข้าชม<span class="resize-handle"></span></th>
                <th data-type="text-short">รอบการเข้าชม<span class="resize-handle"></span></th>
                <th data-type="numeric">ยอดคงเหลือต่อรอบ<span class="resize-handle"></span></th>
                <th data-type="text-long">รายละเอียดการจอง<span class="resize-handle"></span></th>
                <th data-type="text-short">สถานะการจอง<span class="resize-handle"></span></th>
                <th data-type="text-short">แก้ไขสถานะ<span class="resize-handle"></span></th>
            </tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
