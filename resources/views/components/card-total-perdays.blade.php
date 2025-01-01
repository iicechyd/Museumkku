<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    .progress-bar.red {
      background-color: var(--bs-danger);
    }
    .progress-bar.green {
      background-color: var(--bs-success);
    }
    .progress-bar.yellow {
      background-color: var(--bs-warning);
    }
  </style>
</head>
<body>
  <div class="container">
    <h4 class="mb-4">จำนวนผู้เข้าชมต่อตึก</h4>
    <div class="row mb-4">
      @foreach ($activities->whereIn('activity_id', [1, 2]) as $activity)
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">
            <h5>{{ $activity->activity_name }}</h5>
            <h3 class="{{ $activity->activity_id == 1 ? 'text-info' : 'text-danger' }}">
              {{ number_format($totalVisitors[$activity->activity_id]) }} คน
            </h3>          
          </div>
        </div>
      </div>
      @endforeach
      
      @php
        $totalActivity3Visitors = isset($totalVisitors[3]) ? $totalVisitors[3] : 0;
      @endphp
  
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">
            <h5>เข้าชมทั้งสองพิพิธภัณฑ์</h5>
            <h3 class="text-success">{{ number_format($totalActivity3Visitors) }} คน
            </h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">
            <h5>ผู้เข้าชมทั้งหมด</h5>
            <h3 class="text-warning">{{ number_format(array_sum($totalVisitors)) }} คน</h3>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <h5>ยอดการจัดกิจกรรมทั้งหมดในปีนี้</h5>
            <div class="mb-2">
              <div class="d-flex justify-content-between">
                <strong>ชื่อกิจกรรม</strong>
                <strong class="text-right">Goal: 12 ครั้ง/ปี</strong>
              </div>
              <div class="progress">
                <div class="progress-bar" style="width:100%"></div>
              </div>
              <div>
                <span class="ml-2 text-sm text-info"> สำเร็จ 4 ครั้ง </span>
                <span class="ml-2 text-sm text-danger">เหลือ 9 ครั้ง</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      

      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <h6>ผู้เข้าชมทั้งหมด</h6>
            <div class="mb-2">
              <strong>เด็ก</strong>
              <div class="progress">
                <div class="progress-bar yellow" style="width: 43%">43%</div>
              </div>
            </div>
            <div class="mb-2">
              <strong>นักเรียน/นักศึกษา</strong>
              <div class="progress">
                <div class="progress-bar yellow" style="width: 37%">37%</div>
              </div>
            </div>
            <div class="mb-2">
              <strong>ผู้ใหญ่/คุณครู</strong>
              <div class="progress">
                <div class="progress-bar yellow" style="width: 37%">37%</div>
              </div>
            </div>
            <div class="mb-2">
              <strong>ผู้พิการ</strong>
              <div class="progress">
                <div class="progress-bar yellow" style="width: 37%">37%</div>
              </div>
            </div>
            <div class="mb-2">
              <strong>ผู้สูงอายุ</strong>
              <div class="progress">
                <div class="progress-bar yellow" style="width: 37%">37%</div>
              </div>
            </div>
            <div class="mb-2">
              <strong>พระภิกษุสงฆ์ /สามเณร</strong>
              <div class="progress">
                <div class="progress-bar yellow" style="width: 37%">37%</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
