<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/_profiler' => [[['_route' => '_profiler_home', '_controller' => 'web_profiler.controller.profiler::homeAction'], null, null, null, true, false, null]],
        '/_profiler/search' => [[['_route' => '_profiler_search', '_controller' => 'web_profiler.controller.profiler::searchAction'], null, null, null, false, false, null]],
        '/_profiler/search_bar' => [[['_route' => '_profiler_search_bar', '_controller' => 'web_profiler.controller.profiler::searchBarAction'], null, null, null, false, false, null]],
        '/_profiler/phpinfo' => [[['_route' => '_profiler_phpinfo', '_controller' => 'web_profiler.controller.profiler::phpinfoAction'], null, null, null, false, false, null]],
        '/_profiler/xdebug' => [[['_route' => '_profiler_xdebug', '_controller' => 'web_profiler.controller.profiler::xdebugAction'], null, null, null, false, false, null]],
        '/_profiler/open' => [[['_route' => '_profiler_open_file', '_controller' => 'web_profiler.controller.profiler::openAction'], null, null, null, false, false, null]],
        '/hello' => [[['_route' => 'hello', '_controller' => 'App\\Controller\\HelloController::hello'], null, null, null, false, false, null]],
        '/publications' => [[['_route' => 'app_publication_getallpublications', '_controller' => 'App\\Controller\\PublicationController::getAllPublications'], null, ['GET' => 0], null, false, false, null]],
        '/type_publications' => [[['_route' => 'app_typepublication_getalltypepublications', '_controller' => 'App\\Controller\\TypePublicationController::getAllTypePublications'], null, ['GET' => 0], null, false, false, null]],
        '/api/singup' => [[['_route' => 'app_firebaseauth_register', '_controller' => 'App\\Controller\\FirebaseAuthController::register'], null, ['POST' => 0], null, false, false, null]],
        '/api/login' => [[['_route' => 'app_firebaseauth_login', '_controller' => 'App\\Controller\\FirebaseAuthController::login'], null, ['POST' => 0], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_(?'
                    .'|error/(\\d+)(?:\\.([^/]++))?(*:38)'
                    .'|wdt/([^/]++)(*:57)'
                    .'|profiler/([^/]++)(?'
                        .'|/(?'
                            .'|search/results(*:102)'
                            .'|router(*:116)'
                            .'|exception(?'
                                .'|(*:136)'
                                .'|\\.css(*:149)'
                            .')'
                        .')'
                        .'|(*:159)'
                    .')'
                .')'
                .'|/u(?'
                    .'|ser/documents/([^/]++)(?'
                        .'|(*:199)'
                    .')'
                    .'|pload_media/([^/]++)(*:228)'
                .')'
                .'|/publication(?'
                    .'|s(?'
                        .'|/(?'
                            .'|([^/]++)(?'
                                .'|(*:271)'
                                .'|/([^/]++)(*:288)'
                            .')'
                            .'|reporting(*:306)'
                        .')'
                        .'|_user/([^/]++)(*:329)'
                    .')'
                    .'|_(?'
                        .'|id/([^/]++)(*:353)'
                        .'|note/([^/]++)/([^/]++)(*:383)'
                    .')'
                    .'|/([^/]++)/([^/]++)(*:410)'
                .')'
                .'|/re(?'
                    .'|porting(?'
                        .'|/([^/]++)(?'
                            .'|/([^/]++)(*:456)'
                            .'|(*:464)'
                        .')'
                        .'|s/publications/([^/]++)(*:496)'
                    .')'
                    .'|servation/(?'
                        .'|([^/]++)/([^/]++)(*:535)'
                        .'|publication/([^/]++)(*:563)'
                        .'|user/([^/]++)(*:584)'
                        .'|([^/]++)/([^/]++)(?'
                            .'|(*:612)'
                        .')'
                    .')'
                .')'
                .'|/type_publication/([^/]++)(*:649)'
                .'|/api/user/([^/]++)(?'
                    .'|(*:678)'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        38 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        57 => [[['_route' => '_wdt', '_controller' => 'web_profiler.controller.profiler::toolbarAction'], ['token'], null, null, false, true, null]],
        102 => [[['_route' => '_profiler_search_results', '_controller' => 'web_profiler.controller.profiler::searchResultsAction'], ['token'], null, null, false, false, null]],
        116 => [[['_route' => '_profiler_router', '_controller' => 'web_profiler.controller.router::panelAction'], ['token'], null, null, false, false, null]],
        136 => [[['_route' => '_profiler_exception', '_controller' => 'web_profiler.controller.exception_panel::body'], ['token'], null, null, false, false, null]],
        149 => [[['_route' => '_profiler_exception_css', '_controller' => 'web_profiler.controller.exception_panel::stylesheet'], ['token'], null, null, false, false, null]],
        159 => [[['_route' => '_profiler', '_controller' => 'web_profiler.controller.profiler::panelAction'], ['token'], null, null, false, true, null]],
        199 => [
            [['_route' => 'app_document_createdocument', '_controller' => 'App\\Controller\\DocumentController::createDocument'], ['userId'], ['POST' => 0], null, false, true, null],
            [['_route' => 'app_document_getdocumentuserbyid', '_controller' => 'App\\Controller\\DocumentController::getDocumentUserById'], ['userId'], ['GET' => 0], null, false, true, null],
            [['_route' => 'app_document_updatedocument', '_controller' => 'App\\Controller\\DocumentController::updateDocument'], ['userId'], ['PUT' => 0], null, false, true, null],
        ],
        228 => [[['_route' => 'app_uploadmedia_uploadfile', '_controller' => 'App\\Controller\\UploadMediaController::uploadFile'], ['userId'], ['POST' => 0], null, false, true, null]],
        271 => [[['_route' => 'app_publication_createpublication', '_controller' => 'App\\Controller\\PublicationController::createPublication'], ['userId'], ['POST' => 0], null, false, true, null]],
        288 => [[['_route' => 'app_publication_deletepublication', '_controller' => 'App\\Controller\\PublicationController::deletePublication'], ['userId', 'documentId'], ['DELETE' => 0], null, false, true, null]],
        306 => [[['_route' => 'app_reporting_getreportedpublications', '_controller' => 'App\\Controller\\ReportingController::getReportedPublications'], [], ['GET' => 0], null, false, false, null]],
        329 => [[['_route' => 'app_publication_getallpublicationsbyuserid', '_controller' => 'App\\Controller\\PublicationController::getAllPublicationsByUserId'], ['userId'], ['GET' => 0], null, false, true, null]],
        353 => [[['_route' => 'app_publication_getpublicationbyid', '_controller' => 'App\\Controller\\PublicationController::getPublicationById'], ['documentId'], ['GET' => 0], null, false, true, null]],
        383 => [[['_route' => 'app_publication_updatenotepublication', '_controller' => 'App\\Controller\\PublicationController::updateNotePublication'], ['userId', 'documentId'], ['PUT' => 0], null, false, true, null]],
        410 => [[['_route' => 'app_publication_updatepublication', '_controller' => 'App\\Controller\\PublicationController::updatePublication'], ['userId', 'documentId'], ['PUT' => 0], null, false, true, null]],
        456 => [[['_route' => 'app_reporting_createreporting', '_controller' => 'App\\Controller\\ReportingController::createReporting'], ['userId', 'publicationId'], ['POST' => 0], null, false, true, null]],
        464 => [
            [['_route' => 'app_reporting_updatereporting', '_controller' => 'App\\Controller\\ReportingController::updateReporting'], ['reportingId'], ['PUT' => 0], null, false, true, null],
            [['_route' => 'app_reporting_deletereporting', '_controller' => 'App\\Controller\\ReportingController::deleteReporting'], ['reportingId'], ['DELETE' => 0], null, false, true, null],
        ],
        496 => [[['_route' => 'app_reporting_getreportingsbypublicationid', '_controller' => 'App\\Controller\\ReportingController::getReportingsByPublicationId'], ['publicationId'], ['GET' => 0], null, false, true, null]],
        535 => [[['_route' => 'app_reservation_createreservation', '_controller' => 'App\\Controller\\ReservationController::createReservation'], ['userId', 'publicationId'], ['POST' => 0], null, false, true, null]],
        563 => [[['_route' => 'app_reservation_getreservationbypublicationid', '_controller' => 'App\\Controller\\ReservationController::getReservationByPublicationId'], ['publicationId'], ['GET' => 0], null, false, true, null]],
        584 => [[['_route' => 'app_reservation_getreservationbyuserid', '_controller' => 'App\\Controller\\ReservationController::getReservationByUserId'], ['userId'], ['GET' => 0], null, false, true, null]],
        612 => [
            [['_route' => 'app_reservation_updatereservation', '_controller' => 'App\\Controller\\ReservationController::updateReservation'], ['userId', 'reservationId'], ['PUT' => 0], null, false, true, null],
            [['_route' => 'app_reservation_deletereservation', '_controller' => 'App\\Controller\\ReservationController::deleteReservation'], ['userId', 'reservationId'], ['DELETE' => 0], null, false, true, null],
        ],
        649 => [[['_route' => 'app_typepublication_gettypepublicationbyid', '_controller' => 'App\\Controller\\TypePublicationController::getTypePublicationById'], ['typeId'], ['GET' => 0], null, false, true, null]],
        678 => [
            [['_route' => 'app_firebaseauth_getuserbyid', '_controller' => 'App\\Controller\\FirebaseAuthController::getUserById'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'app_firebaseauth_updateuserbyid', '_controller' => 'App\\Controller\\FirebaseAuthController::updateUserById'], ['id'], ['PUT' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
