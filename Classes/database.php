<?php

class database{
    
function opencon(): PDO{
return new PDO("mysql:host=localhost;
dbname=bakla",
username: "root",
password: "");
}

function InsertUser($email, $user_password_hash, $is_active, $member_since){
 $con = $this->opencon();
 try{
    $con->beginTransaction();
    $stmt = $con->prepare("INSERT INTO Users(username, user_password_hash, is_active) VALUES(?,?,?)");
    $stmt->execute([$email,$user_password_hash,$is_active]);
    $user_id =$con->lastInsertId();
    $con->commit();
return $user_id;
 }catch(PDOException $e){
    if($con->inTransaction()){
    $con->rollBack();
    }
    throw $e;
}
}
function InsertBorrower($firstname, $lastname, $is_active, $member_since){
 $con = $this->opencon();
 try{
    $con->beginTransaction();
    $stmt = $con->prepare("INSERT INTO Borrowers(borrower_id, borrower_firstname, borrower_lastname,borrower_email,borrower_phone_number, borrower_member_since, is_active) VALUES(?,?,?,?,?,?)");
    $stmt->execute([$firstname,$lastname,$is_active]);
    $user_id =$con->lastInsertId();
    $con->commit();
return $user_id;
 }catch(PDOException $e){
    if($con->inTransaction()){
    $con->rollBack();
    }
    throw $e;
}
}
function InsertBorrowerUser($email, $user_password_hash, $is_active, $member_since){
 $con = $this->opencon();
 try{
    $con->beginTransaction();
    $stmt = $con->prepare("INSERT INTO BorrowerUser(bu_id,user_id,borrower_id) VALUES(?,?,?,?)");
    $stmt->execute([$email,$user_password_hash,$is_active]);
    $user_id =$con->lastInsertId();
    $con->commit();
return $user_id;
 }catch(PDOException $e){
    if($con->inTransaction()){
    $con->rollBack();
    }
    throw $e;
}
}
}
