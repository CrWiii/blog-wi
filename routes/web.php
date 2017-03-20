<?php
Route::get('/','PostsController@index')->name('home');
Route::get('/post/create','PostsController@create');
Route::post('/posts','PostsController@store');
Route::get('/posts/{post}','PostsController@show');

Route::post('/posts/{post}/comments','CommentsController@store');

Route::get('/register','RegistrationController@create');
Route::post('/register','RegistrationController@store');
Route::get('/login','SessionsController@create');
Route::get('/logout','SessionsController@destroy');
Route::get('getData','ProcessgetdataController@getData');


/*
https://forums.meteor.com/t/full-calendar-modal-with-multiple-event-details/11177

https://www.codeproject.com/Articles/638674/Full-calendar-A-complete-web-diary-syhttps://www.jqueryajaxphp.com/fullcalendar-crud-with-jquery-and-php/stem-for-jQue
https://cdn.auth0.com/blog/laravel-auth/mvc-diagram.png
https://github.com/MikeSmithDev/FullCalModal-Node/tree/master/app
https://code.tutsplus.com/courses/build-a-cms-with-laravel

https://code.tutsplus.com/courses/build-a-cms-with-laravel
https://github.com/LavaLite/cms
https://github.com/ozdemirburak/laravel-5-simple-cms
http://www.lavalite.org/
https://pyrocms.com/
https://github.com/ozdemirburak/laravel-5-simple-cms
https://tympanus.net/Tutorials/InteractiveSVG/
https://tympanus.net/codrops/2013/02/06/interactive-infographic-with-svg-and-css-animations/#the-javascript

https://blog.pisyek.com/create-room-booking-system-laravel-fullcalendar/2/

https://blog.pisyek.com/create-room-booking-system-laravel-fullcalendar-part-2/2/

https://scotch.io/tutorials/ultimate-guide-on-sending-email-in-laravel

https://www.jqueryajaxphp.com/fullcalendar-crud-with-jquery-and-php/
https://github.com/Zizaco/entrust/tree/master#installation

http://jsfiddle.net/welnn/e2z0s43f/

https://blog.pisyek.com/create-room-booking-system-laravel-fullcalendar/2/

https://github.com/CrWiii/startup
https://codyhouse.co/gem/horizontal-timeline/
https://codyhouse.co/demo/vertical-timeline/index.html

https://blog.pisyek.com/create-room-booking-system-laravel-fullcalendar-part-3/
Hola Manuel, sorry la demora, te estoy adjuntando los logos de las marcas que sí van en la web. 
Algunas se han retirado. Arriba siempre las tres principales: Mikimoto, Patek y Mimí. Las otras marcas pueden ir abajo. Los logos deberían ir en esa tonalidad:
R: 103
G: 103
B: 103
Y con el mismo efecto que tenemos en la web actual. 
Las direcciones de abajo también tendrían que ir en esa tonalidad de texto, que es la misma del texto de las redes. 
Estoy poniendo en un word todos los apuntes. Se los envío hoy por la tarde.
Gracias!
Abrazo.
*/