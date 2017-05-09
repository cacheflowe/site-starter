var areaModel = new AreaModel(function(index) {
  page('', index);
  page('/', index);
  page('/home', index);
  page('/home/:id', index);
  page('/home/:section', index);
  page('/home/:section/:id', index);
  page('/about', index);
  page('/contact', index);
  page('/collection', index);
  page('/collection/:id', index);
  page('/music', function(){ page.redirect('/music/discography'); });
  page('/music/:id', index);
  page('/music/:section', index);
  page('/music/:section/:id', index);
  page('/code', function(){ page.redirect('/code/installation'); });
  page('/code/:id', index);
  page('/code/:section', index);
  page('/code/:section/:id', index);
  page('/art', function(){ page.redirect('/art/physical'); });
  page('/art/:id', index);
  page('/art/:section', index);
  page('/art/:section/:id', index);
  page('/test/feed', index);
  page('/test/feed/:id', index);
  // page('*', notfound);
  page();
});