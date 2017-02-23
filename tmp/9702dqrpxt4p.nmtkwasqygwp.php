        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h2 class="sub-header">Recently Added</h2>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Type</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach (($result?:[]) as $item): ?>
                <tr><td><?php echo $item['ID']; ?></td><td><?php echo $item['NAME']; ?></td><td><?php echo $item['CLASS']; ?></td></tr>
              <?php endforeach; ?>
              </tbody>
            </table>
            <nav aria-label="MORE">
            <ul class="pagination">
              <li class="disabled"><a href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
              <li class="active"><a href="#">1 <span class="sr-only">(current)</span></a></li>
              <li><a href="#">2</a></li>
              <li><a href="#">3</a></li>
              <li><a href="#">4</a></li>
              <li><a href="#">5</a></li>
              <li>
                <a href="#" aria-label="Next">
                  <span aria-hidden="true">&raquo;</span>
                </a>
              </li>
            </ul>
          </nav>
          </div>
        </div>