<?php
session_start();
require 'db-connect.php';
require 'header.php';
require 'menu.php';
?>

<div class="container is-max-desktop p-5 mt-5 has-background-white">
    <h1 class="title is-3 has-text-centered mb-5">お支払方法の選択</h1>

    <div class="box"> <form action="payment-success.php" method="post">
            <div class="field">
                <p class="subtitle is-5 has-text-weight-bold mb-4">支払い方法を選んでください</p>
                
                <div class="control mb-3">
                    <label class="radio is-size-5">
                        <input type="radio" name="choice" value="kure" required>
                        <span class="has-text-weight-semibold ml-2">クレジットカードでお支払い</span>
                    </label>
                </div>

                <div class="control mb-3">
                    <label class="radio is-size-5">
                        <input type="radio" name="choice" value="pei">
                        <span class="has-text-weight-semibold ml-2">PayPayでお支払い</span>
                    </label>
                </div>

                <div class="control mb-3">
                    <label class="radio is-size-5">
                        <input type="radio" name="choice" value="app">
                        <span class="has-text-weight-semibold ml-2">Apple Payでお支払い</span>
                    </label>
                </div>

                <div class="control mb-3">
                    <label class="radio is-size-5">
                        <input type="radio" name="choice" value="gen">
                        <span class="has-text-weight-semibold ml-2">コンビニでお支払い</span>
                    </label>
                </div>
                
            </div>
            
            <hr class="mt-5">

            <div class="has-text-centered">
                <button type="submit" class="button is-primary is-large is-rounded is-fullwidth">
                    <span class="icon"><i class="fas fa-check"></i></span>
                    <span>決済を確定する</span>
                </button>
            </div>
        </form>
    </div> <div class="mt-4">
        <form action="cart-show.php" method="get">
            <button type="submit" class="button is-light is-fullwidth">
                <span class="icon"><i class="fas fa-arrow-left"></i></span>
                <span>キャンセルしてカートに戻る</span>
            </button>
        </form>
    </div>
</div>

<?php require 'footer.php'; ?>