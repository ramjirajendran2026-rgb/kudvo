<style>
    footer {
        width: 100%;
        text-align: end;
        font-size: 12px;
        padding-right: 10mm;
    }
</style>

<footer>
    {{ $election->getRouteKey() }} -
    @pageNumber
    of
    @totalPages
</footer>
