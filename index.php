<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Book Tracker</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 2rem;
      background-color: #f9f9f9;
    }
    .card {
      background-color: #fff;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      margin-bottom: 2rem;
      text-align: center;
    }
    h1 {
      margin: 0;
      font-size: 2rem;
    }
    h2 {
      margin: 0.5rem 0 0;
      font-weight: normal;
      font-size: 1.2rem;
      color: #555;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
    }
    th, td {
      padding: 0.75rem;
      border: 1px solid #ddd;
    }
    th {
      background-color: #f2f2f2;
      text-align: left;
    }
  </style>
</head>
<body>

  <div class="card">
    <h1>Book Tracker</h1>
    <h2>by Your Name</h2>
  </div>

  <table id="book-table">
    <thead>
      <tr>
        <th>ISBN</th>
        <th>Title</th>
        <th>Author</th>
        <th>Year Published</th>
      </tr>
    </thead>
    <tbody>
      <!-- Rows will be added by JS -->
    </tbody>
  </table>

  <script>
    window.addEventListener('DOMContentLoaded', async () => {
      try {
        const response = await fetch('/booktracker/api/index.php');
        const books = await response.json();

        const tbody = document.querySelector('#book-table tbody');
        books.forEach(book => {
          const row = document.createElement('tr');
          row.innerHTML = `
            <td>${book.isbn}</td>
            <td>${book.title}</td>
            <td>${book.author}</td>
            <td>${book.year_published ?? ''}</td>
          `;
          tbody.appendChild(row);
        });
      } catch (error) {
        alert("Failed to load book data.");
        console.error(error);
      }
    });
  </script>

</body>
</html>
