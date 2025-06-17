
package br.com.unicuritiba.bancocentral.service;

import br.com.unicuritiba.bancocentral.model.Customer;
import br.com.unicuritiba.bancocentral.repository.CustomerRepository;
import java.util.List;
import java.util.Optional;

import org.springframework.stereotype.Service;


@Service
public class CustomerService {
    private final CustomerRepository repository;

    public CustomerService(CustomerRepository repository) {
        this.repository = repository;
    }

    public List<Customer> findAll() {
        return repository.findAll();
    }

    public Optional<Customer> findById(Long id) {
        return repository.findById(id);
    }

    public Customer save(Customer obj) {
        return repository.save(obj);
    }

    public void delete(Long id) {
        repository.deleteById(id);
    }
}
