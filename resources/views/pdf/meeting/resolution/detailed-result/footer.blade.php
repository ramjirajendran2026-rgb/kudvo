<style>
    footer {
        width: 100%;
        text-align: end;
        font-size: 16px;
        padding-right: 10mm;
    }
</style>

<footer>
    {{ $meeting->getRouteKey() }} - @pageNumber of @totalPages
</footer>
