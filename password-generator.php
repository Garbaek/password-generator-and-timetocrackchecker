<?php
function generatePassword($length = 32) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+[]{}|;:,.<>?';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
}

function loadCommonWords() {
    $commonWords = [];
    
    if (file_exists('danish.txt')) {
        $content = file_get_contents('danish.txt');
        $commonWords = array_map('trim', explode(',', $content));
    }
    
    if (file_exists('leaked_passwords.txt')) {
        $leakedPasswords = file('leaked_passwords.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $commonWords = array_merge($commonWords, $leakedPasswords);
    }

    return array_map('strtolower', array_filter($commonWords));
}

function containsCommonWords($password, $commonWords) {
    $passwordLower = strtolower($password);
    foreach ($commonWords as $word) {
        if (strlen($word) >= 4 && stripos($passwordLower, $word) !== false) {
            return true;
        }
    }
    return false;
}

function estimateCrackTime($password) {
    $commonWords = loadCommonWords();
    $containsCommon = containsCommonWords($password, $commonWords);
    
    // If the password is exactly a common word, it's instantly crackable
    if (in_array(strtolower($password), $commonWords)) {
        return "Instantly (leaked password)";
    }

    // Calculate password strength score
    $score = 0;

    // Length: longer passwords get more points
    $length = strlen($password);
    $score += $length * 4;

    // Character diversity: mix of character types increases score
    $charTypes = 0;
    if (preg_match('/[a-z]/', $password)) $charTypes++;
    if (preg_match('/[A-Z]/', $password)) $charTypes++;
    if (preg_match('/[0-9]/', $password)) $charTypes++;
    if (preg_match('/[^a-zA-Z0-9]/', $password)) $charTypes++;
    $score += $charTypes * 10;

    // Deduct points for repeated characters or simple patterns
    $uniqueChars = count(array_unique(str_split($password)));
    if ($uniqueChars < $length) {
        $score -= ($length - $uniqueChars) * 5;
    }

    // Deduct points for common words
    if ($containsCommon) {
        $score -= 50; // Significant penalty for common words
    }

    // Ensure score is not negative
    $score = max($score, 0);

    // Estimate crack time based on score
    if ($score < 20) {
        $crackTime = "Instantly (very weak password)";
    } elseif ($score < 40) {
        $crackTime = "Less than a minute";
    } elseif ($score < 60) {
        $crackTime = "A few minutes";
    } elseif ($score < 80) {
        $crackTime = "A few hours";
    } elseif ($score < 100) {
        $crackTime = "A few days";
    } elseif ($score < 120) {
        $crackTime = "A few months";
    } else {
        $crackTime = "Many years";
    }

    // Add warning if common words are present
    if ($containsCommon) {
        $crackTime .= " (Warning: contains common words)";
    }

    return $crackTime;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['generate'])) {
        $length = isset($_POST['length']) ? (int) $_POST['length'] : 32;
        $password = generatePassword($length);
    }
    if (isset($_POST['check'])) {
        $passwordToCheck = $_POST['password'] ?? '';
        $crackTime = estimateCrackTime($passwordToCheck);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Generator & Checker</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin: 20px; }
        input, button { margin: 10px; padding: 10px; }
    </style>
</head>
<body>
    <h1>Password Generator & Strength Checker</h1>
    
    <form method="post">
        <label for="length">Password Length:</label>
        <input type="range" id="length" name="length" min="32" max="64" value="32" oninput="this.nextElementSibling.value = this.value">
        <output>32</output>
        <br>
        <button type="submit" name="generate">Generate Password</button>
    </form>
    
    <?php if (!empty($password)): ?>
        <p><strong>Generated Password:</strong> <input type="text" value="<?= htmlspecialchars($password) ?>" readonly></p>
    <?php endif; ?>
    
    <form method="post">
        <label for="password">Check Password Strength:</label>
        <input type="text" id="password" name="password" required>
        <br>
        <button type="submit" name="check">Check Strength</button>
    </form>
    
    <?php if (isset($crackTime)): ?>
        <p><strong>Estimated Crack Time:</strong> <?= htmlspecialchars($crackTime) ?></p>
    <?php endif; ?>
</body>
</html>
