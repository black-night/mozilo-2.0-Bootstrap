$(document).ready(function(){
  function switchElements(oldID, newID) {
    if(!isNaN(newID)){
      var tempImg = $(":text[name=bsCarouselItemImg"+oldID+"]").val();      
      var tempContent = $("[name=bsCarouselItemContent"+oldID+"]").val();
      var tempSelectImg = $("[name=bsCarouselItemSelectImg"+oldID+"]").val();
      $("[name=bsCarouselItemSelectImg"+oldID+"]").val($("[name=bsCarouselItemSelectImg"+newID+"]").val());
      $(":text[name=bsCarouselItemImg"+oldID+"]").val($(":text[name=bsCarouselItemImg"+newID+"]").val());
      $("[name=bsCarouselItemContent"+oldID+"]").val($("[name=bsCarouselItemContent"+newID+"]").val());
      $("[name=bsCarouselItemSelectImg"+newID+"]").val(tempSelectImg);  
      $(":text[name=bsCarouselItemImg"+newID+"]").val(tempImg);
      $("[name=bsCarouselItemContent"+newID+"]").val(tempContent);    
    }  
  }
  function bsMoveSlideUpClick() {
    var oldID = parseInt($(this).parent().parent().data("elementid"),10);
    var newID = parseInt($(this).parent().parent().prev().data("elementid"),10);
    switchElements(oldID,newID);
  };
  function bsMoveSlideDownClick() {
    var oldID = parseInt($(this).parent().parent().data("elementid"),10);
    var newID = parseInt($(this).parent().parent().next().data("elementid"),10);
    switchElements(oldID,newID);
  };  
  function bsAddSlideClick() {
    var countElement = parseInt($(":hidden[name=bsSlideMaxID]").val(),10); 
		$(":hidden[name=bsSlideMaxID]").val(countElement+1);
		$("#bsCarouselElements").append(newElement.replace(/XXX/g,countElement));
  };
  function bsDeleteSlideClick() {
    $(this).parent().parent().remove();
  }
  function bsCarouselItemSelectImgClick() {
    $(":text[name="+$(this).data("destination")+"]").attr("value",this.value);
  }
  function bsBtnCarouselFunctionClick() {
    window.location = $(this).data("link");
  }
  function registerEvents() {  
    $("#bsCarouselElements").on('click', ':button[name=bsMoveSildeUp]',bsMoveSlideUpClick);
    $("#bsCarouselElements").on('click', ':button[name=bsMoveSildeDown]',bsMoveSlideDownClick);
    $("#bsCarouselElements").on('click', ':button[name=bsDeleteSlide]',bsDeleteSlideClick);    
    $("#bsCarouselElements").on('click', 'select',bsCarouselItemSelectImgClick);    
    $("#bsAllCarousels").on('click', '.btn-carousel-function',bsBtnCarouselFunctionClick);        
    $(":button[name=bsAddSlide]").click(bsAddSlideClick);
  };
  registerEvents();
});  