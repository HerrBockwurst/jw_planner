<?php
session_start();
require_once 'jwplanner.php';

(new JWPlanner())->deliverContent();
