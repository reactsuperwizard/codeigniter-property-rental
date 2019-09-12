<div class="modal fade" id="testLongModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php $this->load->helper('lorem_ipsum'); $content=lipsum(15,'long',array('ul'=>1,'ol'=>1,'d'=>1,'a'=>1,'co'=>1,'dl'=>1,'bq'=>1,'h'=>1));
        echo str_replace(array('</h2>','</h3>','</h4>'),array('</h2>'.'<iframe width="560" height="315" src="https://www.youtube.com/embed/1t1OL2zN0LQ" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>','</h3>'.'<iframe src="https://player.vimeo.com/video/293033666" width="640" height="268" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
<p><a href="https://vimeo.com/293033666">FAUVE</a> from <a href="https://vimeo.com/jcomte">Jeremy Comte</a> on <a href="https://vimeo.com">Vimeo</a>.</p>','</h4>'.'<iframe src="//coub.com/embed/1i7rjw?muted=false&autostart=false&originalSize=false&startWithHD=false" allowfullscreen frameborder="0" width="640" height="360" allow="autoplay"></iframe>'),$content); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>