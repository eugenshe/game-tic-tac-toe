var game = {};
$(document).ready(function(){
    function initGame(){
        game.gameBoard 	 = [['','',''],['','',''],['','','']];
        game.players = {1:"X",2:"O"};
        game.isClick = 0;
        game.toggleBoardClick = function(){
            if (0 == this.isClick){
                this.enableBoardClick();
                this.isClick = 1;
                return ;
            };
            this.disableBoardClic();
            this.isClick = 0;
        };
        game.getPlayer = function(p){
            if (isNaN(p)){
                return (game.players[1] == p)? 1:2;
            };
            return (1 == p)? game.players[1]:game.players[2];
        };

        game.getCurrentBoard = function(){
            $('td').each(function(){
                var $This = $(this),player = $This.text();
                var pos1 = parseInt($This.parent().attr('id').replace('r','')), pos2=parseInt($This.attr('class').replace('t',''));
                if (player != ''){
                    player = game.getPlayer(player);
                    game.gameBoard[pos1][pos2] = player;
                };
            });
            return this.gameBoard;
        };

        game.setPosition = function(pos1,pos2,player,obj){
            this.gameBoard[pos1][pos2] = this.getPlayer(player);
            if (2 == this.gameBoard[pos1][pos2]){
                $('td').each(function(key,val){
                    if (pos1 == $(this).parent().attr('id').replace('r','') && pos2 == $(this).attr('class').replace('t','')){
                        $(this).text(player);
                        return ;
                    };
                });
            } else {
                obj.text(player);
            };
        };

        game.enableBoardClick = function(){
            $('td').unbind('click').click(function(){
                var $This = $(this);
                if ($This.text() == ''){
                    var pos1 			=	parseInt($This.parent().attr('id').replace('r','')),
                        pos2			=	parseInt($This.attr('class').replace('t','')),
                        currentBoard	= 	$.extend(true, {}, game.getCurrentBoard());
                    game.disableBoardClick('Please wait..');
                    game.setPosition(pos1,pos2,game.players[1],$This);
                    $.ajax({
                        type: "POST",
                        url: "Request/RequestHandler.php",
                        data: {pos1:pos1,pos2:pos2,gameBoard:currentBoard},
                        success: function(msg){
                            var jobj = JSON.parse(msg);
                            if (jobj.move){
                                game.setPosition(jobj.move[0],jobj.move[1],game.players[2]);
                                game.enableBoardClick();
                            };
                            if (jobj.errorMessage){
                                switch(jobj.errorMessage[0]){
                                    case 400:
                                        $('#info').text('It\'s a draw!');
                                        break;
                                    default:
                                        setTimeout(function(){
                                            winner = game.getPlayer(jobj.errorMessage);
                                            if (confirm("Player " + winner + " won, do you want to try again?")){
                                                window.location.reload(true);
                                            };
                                            game.disableBoardClick("Game is over, the winner is: " + winner);
                                        },500);
                                };
                            };
                        }
                    });
                };
            });
        };

        game.disableBoardClick = function(text){
            $('td').unbind('click').click(function(){
                $('#info').text(text);
            });
        };
    };



    initGame();

    game.enableBoardClick();

    $("#restart").mousedown(function(){
        $(this).addClass('clicked');
    }).mouseup(function(){
        window.location.reload(true);
    });
});