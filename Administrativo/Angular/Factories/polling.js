(function () {
    'use strict';

    //var pollingService = angular.module("services.polling", []);

    app
        .factory('$polling', function ($http) {

            var defaultPollingTime = 10000;
            var polls = [];

            return {
                startPolling: function (name, url, pollingTime, callback,data) {

                    if(!polls[name]) {
                        var poller = function () {
                            return $http.post(url,data).then(function (response) {
                                callback(response);
                            });
                        }
                    }
                    poller();
                    polls[name] = setInterval(poller, pollingTime || defaultPollingTime);

                },

                stopPolling: function (name) {
                    clearInterval(polls[name]);
                    delete polls[name];
                }
            }
        });

}());