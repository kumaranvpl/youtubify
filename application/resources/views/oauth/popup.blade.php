<h1 style="text-align: center">Logging in</h1>

<script>
    try {
        window.opener.$tempScope.socialLoginCallback({!! $user !!})
    } catch(err) {}
    window.close();
</script>
