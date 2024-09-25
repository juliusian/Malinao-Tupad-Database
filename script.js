
let rowCount = 0;

function addRow() {
    const table = document.getElementById('dynamicTable').getElementsByTagName('tbody')[0];
    const newRow = table.insertRow();
    for (let i = 0; i < 20; i++) {
        const newCell = newRow.insertCell(i);
        newCell.contentEditable = 'true'; 
        newCell.innerHTML = '';
    }
    const actionCell = newRow.insertCell(20);
    actionCell.innerHTML = `<button class="btn delete-btn" onclick="deleteRow(this)">Delete</button>`;
}

function deleteRow(button) {
    const row = button.parentNode.parentNode;
    row.parentNode.removeChild(row);
}

document.getElementById('tableForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission

    const table = document.getElementById('dynamicTable');
    const rows = table.getElementsByTagName('tbody')[0].rows;

    let tableData = [];
    for (let i = 0; i < rows.length; i++) {
        let rowData = [];
        for (let j = 0; j < 20; j++) {
            let cellValue = rows[i].cells[j].innerText.trim();
            if (cellValue === "") {
                alert('Please fill all fields before saving.');
                return;
            }
            rowData.push(cellValue);
        }
        tableData.push(rowData);
    }

    const tableNameInput = document.getElementById('tableNameInput');
    if (tableNameInput.value.trim() === '') {
        alert("Please enter a table name.");
        return;
    }

    fetch('save_table.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            table_name: tableNameInput.value.trim(),
            table_data: JSON.stringify(tableData)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
        } else if (data.success) {
            alert(data.success);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An unexpected error occurred.');
    });
});

function toggleView() {
    const savedTablesDiv = document.getElementById('savedTables');
    savedTablesDiv.classList.toggle('hidden');

    if (!savedTablesDiv.classList.contains('hidden')) {
        displaySavedTables();
    }
}

function displaySavedTables() {
    fetch('retrieve_tables.php')
    .then(response => response.json())
    .then(data => {
        const tableContainer = document.getElementById('tableContainer');
        tableContainer.innerHTML = '';

        if (data.error) {
            tableContainer.innerHTML = `<p>Error: ${data.error}</p>`;
            return;
        }

        if (data.message) {
            tableContainer.innerHTML = `<p>${data.message}</p>`;
            return;
        }

        if (!Array.isArray(data)) {
            tableContainer.innerHTML = '<p>Unexpected data format.</p>';
            return;
        }

        const tableList = document.createElement('ul');
        data.forEach(tableName => {
            const listItem = document.createElement('li');
            listItem.innerHTML = `<a href="#" onclick="showTableContent('${tableName}')">${tableName}</a>`;
            tableList.appendChild(listItem);
        });
        tableContainer.appendChild(tableList);
    })
    .catch(error => console.error('Error fetching tables:', error));
}

function showTableContent(tableName) {
    fetch(`retrieve_table_content.php?table_name=${encodeURIComponent(tableName)}`)
    .then(response => response.json())
    .then(data => {
        const tableContentSaved = document.getElementById('tableContentSaved');
        const tableNameHeading = document.getElementById('tableNameHeading');
        const contentTableSaved = document.getElementById('contentTableSaved');

        tableNameHeading.innerText = tableName;
        const tbody = contentTableSaved.getElementsByTagName('tbody')[0];
        tbody.innerHTML = '';

        if (data.error) {
            alert(data.error);
            return;
        }

        data.forEach(row => {
            const newRow = tbody.insertRow();
            row.forEach(cell => {
                const newCell = newRow.insertCell();
                newCell.innerText = cell;
            });
        });

        tableContentSaved.classList.remove('hidden');
    })
    .catch(error => console.error('Error fetching table content:', error));
}

document.addEventListener('DOMContentLoaded', () => {
    const searchButton = document.getElementById('searchButton');
    const searchInput = document.getElementById('searchInput');

    searchButton.addEventListener('click', () => {
        const searchTerm = searchInput.value.trim();
        if (searchTerm) {
            fetch(`search.php?term=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(data => {
                const tableContentSearch = document.getElementById('tableContentSearch');
                const contentTableSearch = document.getElementById('contentTableSearch');
                const tbody = contentTableSearch.getElementsByTagName('tbody')[0];

                tbody.innerHTML = '';

                if (data.error) {
                    alert(data.error);
                    return;
                }

                data.forEach(row => {
                    const newRow = tbody.insertRow();
                    row.forEach(cell => {
                        const newCell = newRow.insertCell();
                        newCell.innerText = cell;
                    });
                });

                tableContentSearch.classList.remove('hidden');
            })
            .catch(error => console.error('Error searching:', error));
        } else {
            alert("Please enter a search term.");
        }
    });
});
